<?php

namespace App\Http\Controllers;

use App\Mail\OrderSuccessMail;
use App\Models\BranchModel;
use App\Models\CartModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\PayMethodModel;
use App\Models\ProductVariantModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN CHECKOUT (dari keranjang atau Buy Now)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        /*
    |--------------------------------------------------------------------------
    | BUY NOW MODE
    |--------------------------------------------------------------------------
    */
        if ($request->filled('variant_id') && $request->filled('qty')) {

            $variant = ProductVariantModel::with('product')
                ->find($request->variant_id);

            if (!$variant) {
                return redirect()
                    ->route('customer.product')
                    ->with('error', 'Variant tidak ditemukan');
            }

            if ($variant->stock < $request->qty) {
                return back()->with('error', 'Stock tidak cukup');
            }

            // ✅ hanya branch yg punya variant ini & stock tersedia
            $branches = BranchModel::active()
                ->whereHas('productVariants', function ($q) use ($variant) {
                    $q->where('id', $variant->id)
                        ->where('stock', '>', 0);
                })
                ->get();

            $buyNowItem = (object)[
                'variant' => $variant,
                'qty' => $request->qty
            ];

            $paymentMethods = PayMethodModel::where('is_active', true)->get();

            return view('customer.checkout', [

                'title' => 'Buat Pesanan | Trendora',
                'navlink' => 'Buat Pesanan',

                'buyNowItem' => $buyNowItem,

                'branches' => $branches,

                'paymentMethods' => $paymentMethods,

                'total' => $variant->price * $request->qty,

                'isBuyNow' => true
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | CART MODE
    |--------------------------------------------------------------------------
    */

        $cart = CartModel::with([
            'items.variant.product'
        ])
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {

            return redirect()
                ->route('customer.cart.index')
                ->with('error', 'Keranjang kosong');
        }

        // ambil variant ids dari cart
        $variantIds = $cart->items->pluck('variant_id');

        // ✅ hanya branch yg punya variant di cart & stock tersedia
        $branches = BranchModel::active()
            ->whereHas('productVariants', function ($q) use ($variantIds) {
                $q->whereIn('id', $variantIds)
                    ->where('stock', '>', 0);
            })
            ->get();

        if ($branches->isEmpty()) {
            return back()->withErrors([
                'branch' => 'Tidak ada cabang yang memiliki produk tersedia'
            ]);
        }

        $paymentMethods = PayMethodModel::where('is_active', true)->get();

        $total = $cart->items->sum(function ($item) {

            return $item->variant->price * $item->qty;
        });

        return view('customer.checkout', [

            'title' => 'Checkout | Trendora',

            'navlink' => 'Checkout',

            'cart' => $cart,

            'branches' => $branches,

            'paymentMethods' => $paymentMethods,

            'total' => $total,

            'isBuyNow' => false
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES CHECKOUT (dari keranjang)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'branch_id' => 'required|exists:branches,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'qty' => 'nullable|integer|min:1'
        ]);

        $user = Auth::user();

        try {

            $order = DB::transaction(function () use ($request, $user) {

                $totalPrice = 0;

                $payment = PayMethodModel::findOrFail(
                    $request->payment_method_id
                );

                $branchValid = ProductVariantModel::where('branch_id', $request->branch_id)
                    ->where('stock', '>', 0)
                    ->exists();

                if (!$branchValid) {
                    throw new \Exception('Cabang tidak memiliki produk tersedia');
                }

                /*
                |------------------------------------------------------------------
                | BUY NOW
                |------------------------------------------------------------------
                */
                if ($request->filled('variant_id') && $request->filled('qty')) {

                    $variant = ProductVariantModel::lockForUpdate()
                        ->with('product')
                        ->where('branch_id', $request->branch_id)
                        ->findOrFail($request->variant_id);

                    if ($variant->stock < $request->qty) {
                        throw new \Exception(
                            'Stok tidak cukup untuk ' . $variant->product->name
                        );
                    }

                    $totalPrice = $variant->price * $request->qty;

                    $order = OrderModel::create([
                        'user_id' => $user->id,
                        'branch_id' => $request->branch_id,
                        'payment_method_id' => $payment->id,
                        'total_price' => $totalPrice,
                        'bank_name' => $payment->bank_name,
                        'bank_account_number' => $payment->account_number,
                        'bank_account_name' => $payment->account_name,
                        'payment_status' => 'pending',
                        'status' => 'pending',
                        'note' => $request->note
                    ]);

                    OrderItemModel::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $variant->id,
                        'quantity' => $request->qty,
                        'price' => $variant->price,
                        'subtotal' => $totalPrice,
                        'product_name' => $variant->product->name,
                        'variant_sku' => $variant->sku,
                        'variant_color' => $variant->color,
                        'variant_size' => $variant->size,
                    ]);

                    $variant->decrement('stock', $request->qty);
                }

                /*
                |------------------------------------------------------------------
                | CART MODE
                |------------------------------------------------------------------
                */ else {

                    $cart = CartModel::with(['items.variant.product'])
                        ->where('user_id', $user->id)
                        ->firstOrFail();

                    if ($cart->items->isEmpty()) {
                        throw new \Exception('Keranjang kosong');
                    }

                    $order = OrderModel::create([
                        'user_id' => $user->id,
                        'branch_id' => $request->branch_id,
                        'payment_method_id' => $payment->id,
                        'total_price' => 0,
                        'bank_name' => $payment->bank_name,
                        'bank_account_number' => $payment->account_number,
                        'bank_account_name' => $payment->account_name,
                        'payment_status' => 'pending',
                        'status' => 'pending',
                        'note' => $request->note
                    ]);

                    foreach ($cart->items as $item) {

                        $variant = ProductVariantModel::lockForUpdate()
                            ->with('product')
                            ->where('branch_id', $request->branch_id)
                            ->find($item->variant_id);

                        if (!$variant) continue;

                        if ($variant->stock < $item->qty) {
                            throw new \Exception(
                                'Stok tidak cukup untuk ' . $variant->product->name
                            );
                        }

                        $subtotal = $variant->price * $item->qty;
                        $totalPrice += $subtotal;

                        OrderItemModel::create([
                            'order_id' => $order->id,
                            'product_variant_id' => $variant->id,
                            'quantity' => $item->qty,
                            'price' => $variant->price,
                            'subtotal' => $subtotal,
                            'product_name' => $variant->product->name,
                            'variant_sku' => $variant->sku,
                            'variant_color' => $variant->color,
                            'variant_size' => $variant->size,
                        ]);

                        $variant->decrement('stock', $item->qty);
                    }

                    $order->update([
                        'total_price' => $totalPrice
                    ]);

                    $cart->items()->delete();
                }

                return $order; // ✅ INI KUNCI NYA
            });

            // ✅ kirim email DI SINI (AMAN)
            Mail::to($user->email)->send(new OrderSuccessMail($order));

            return redirect()
                ->route('customer.orders')
                ->with('success', 'Pesanan berhasil dibuat');
        } catch (\Throwable $e) {

            return back()->withErrors([
                'checkout' => $e->getMessage()
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BUY NOW (langsung dari produk)
    |--------------------------------------------------------------------------
    */
    public function buyNow(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'qty' => 'required|integer|min:1'
        ]);

        $variant = ProductVariantModel::find($request->variant_id);

        if (!$variant) {
            return back()->with('error', 'Variant tidak ditemukan');
        }

        if ($variant->stock < $request->qty) {
            return back()->with('error', 'Stok tidak cukup');
        }

        // 🔥 redirect pakai query param
        return redirect()->route('customer.checkout', [
            'variant_id' => $variant->id,
            'qty' => $request->qty
        ]);
    }
}
