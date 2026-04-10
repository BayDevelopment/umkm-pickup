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
use Illuminate\Support\Facades\Log;
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
        |------------------------------------------------------------------
        | BUY NOW MODE (FIX: expired_at validation)
        |------------------------------------------------------------------
        */
        if (session()->has('buy_now')) {

            $data = session('buy_now');

            // ✅ VALIDASI EXPIRED
            if (
                !$data ||
                !isset($data['expired_at']) ||
                now()->timestamp > $data['expired_at']
            ) {
                session()->forget('buy_now');

                return redirect()->route('customer.product')
                    ->with('error', 'Session Buy Now sudah expired');
            }

            $variant = ProductVariantModel::with(['product', 'branch'])
                ->find($data['variant_id']);

            if (!$variant) {
                return redirect()->route('customer.product')
                    ->with('error', 'Variant tidak ditemukan');
            }

            if (!$variant->product || !$variant->product->is_active) {
                return redirect()->route('customer.product')
                    ->with('error', 'Produk tidak tersedia');
            }

            if ($variant->stock < $data['qty']) {
                return back()->with('error', 'Stock tidak cukup');
            }

            $branches = BranchModel::active()
                ->where('id', $variant->branch_id)
                ->get();

            $buyNowItem = (object)[
                'variant' => $variant,
                'qty' => $data['qty']
            ];

            $paymentMethods = PayMethodModel::where('is_active', true)->get();

            return view('customer.checkout', [
                'title' => 'Buat Pesanan | Trendora',
                'navlink' => 'Buat Pesanan',
                'buyNowItem' => $buyNowItem,
                'branches' => $branches,
                'paymentMethods' => $paymentMethods,
                'total' => $variant->price * $data['qty'],
                'isBuyNow' => true
            ]);
        }

        /*
        |------------------------------------------------------------------
        | CART MODE
        |------------------------------------------------------------------
        */

        $cart = CartModel::with([
            'items.variant.product',
            'items.variant.branch'
        ])
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('customer.cart.index')
                ->with('error', 'Keranjang kosong');
        }

        $items = $cart->items;

        foreach ($items as $item) {

            if (!$item->variant) {
                return redirect()->route('customer.cart.index')
                    ->withErrors(['cart' => 'Variant tidak valid']);
            }

            if (!$item->variant->product || !$item->variant->product->is_active) {
                return redirect()->route('customer.cart.index')
                    ->withErrors(['cart' => 'Produk tidak tersedia']);
            }

            if ($item->variant->stock < $item->qty) {
                return redirect()->route('customer.cart.index')
                    ->withErrors([
                        'cart' => 'Stok tidak cukup untuk ' . $item->variant->product->name
                    ]);
            }
        }

        // ✅ VALIDASI STRICT SINGLE BRANCH
        $branchIds = $items->pluck('variant.branch_id')->unique();

        if ($branchIds->count() > 1) {
            return redirect()
                ->route('customer.cart.index')
                ->withErrors([
                    'branch' => 'Produk berasal dari cabang berbeda.'
                ]);
        }

        $branchId = $branchIds->first();

        $branches = BranchModel::active()
            ->where('id', $branchId)
            ->get();

        $paymentMethods = PayMethodModel::where('is_active', true)->get();

        $total = $items->sum(function ($item) {
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
        $user = Auth::user();

        // ✅ FIX: VALIDASI USER
        if (!$user) {
            abort(403);
        }

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'branch_id' => 'required|exists:branches,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'qty' => 'nullable|integer|min:1|max:100',
        ]);

        try {

            $order = DB::transaction(function () use ($request, $user) {

                $totalPrice = 0;

                $payment = PayMethodModel::findOrFail($request->payment_method_id);

                if (!$payment->is_active) {
                    throw new \Exception('invalid');
                }

                $branchValid = BranchModel::where('id', $request->branch_id)
                    ->where('is_active', true)
                    ->exists();

                if (!$branchValid) {
                    throw new \Exception('invalid');
                }

                /*
                |---------------- BUY NOW ----------------|
                */
                if ($request->filled('variant_id') && $request->filled('qty')) {

                    $variant = ProductVariantModel::lockForUpdate()
                        ->with('product')
                        ->findOrFail($request->variant_id);

                    if ($variant->branch_id != $request->branch_id) {
                        throw new \Exception('invalid');
                    }

                    if (!$variant->product || !$variant->product->is_active) {
                        throw new \Exception('invalid');
                    }

                    if ($variant->stock < $request->qty) {
                        throw new \Exception('invalid');
                    }

                    $totalPrice = $variant->price * $request->qty;

                    $order = OrderModel::create([
                        'user_id' => $user->id,
                        'branch_id' => $variant->branch_id,
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
                |---------------- CART MODE (FIXED) ----------------|
                */ else {

                    $cart = CartModel::with(['items.variant.product'])
                        ->where('user_id', $user->id)
                        ->firstOrFail();

                    if ($cart->items->isEmpty()) {
                        throw new \Exception('invalid');
                    }

                    // ✅ FIX: VALIDASI CABANG GLOBAL
                    $branchIds = $cart->items->pluck('variant.branch_id')->unique();

                    if ($branchIds->count() > 1) {
                        throw new \Exception('invalid');
                    }

                    $realBranchId = $branchIds->first();

                    // ✅ FIX: CEK MANIPULASI
                    if ($request->branch_id != $realBranchId) {
                        throw new \Exception('invalid');
                    }

                    $order = OrderModel::create([
                        'user_id' => $user->id,
                        'branch_id' => $realBranchId,
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
                            ->findOrFail($item->variant_id);

                        if (!$variant->product || !$variant->product->is_active) {
                            throw new \Exception('invalid');
                        }

                        if ($variant->branch_id != $realBranchId) {
                            throw new \Exception('invalid');
                        }

                        if ($variant->stock < $item->qty) {
                            throw new \Exception('invalid');
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

                        $item->delete();
                    }

                    $order->update([
                        'total_price' => $totalPrice
                    ]);
                }

                return $order;
            });

            Mail::to($user->email)->send(new OrderSuccessMail($order));

            return redirect()
                ->route('customer.orders')
                ->with('success', 'Pesanan berhasil dibuat');
        } catch (\Throwable $e) {

            // ✅ LOG INTERNAL (AMAN)
            Log::error($e);

            return back()->withErrors([
                'checkout' => 'Terjadi kesalahan saat proses checkout'
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
        // ✅ VALIDASI USER (jangan asumsi selalu login)
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $request->validate([
            'variant_id' => 'required|integer|exists:product_variants,id',
            'qty' => 'required|integer|min:1|max:100'
        ]);

        // ✅ AMBIL DATA + RELASI
        $variant = ProductVariantModel::with(['product', 'branch'])
            ->find($request->variant_id);

        if (!$variant) {
            return back()->with('error', 'Variant tidak ditemukan');
        }

        // ✅ VALIDASI PRODUK AKTIF
        if (!$variant->product || !$variant->product->is_active) {
            return back()->with('error', 'Produk tidak tersedia');
        }

        // ✅ VALIDASI CABANG AKTIF (tambahan)
        if (!$variant->branch || !$variant->branch->is_active) {
            return back()->with('error', 'Cabang tidak tersedia');
        }

        // ✅ VALIDASI STOCK
        if ($variant->stock <= 0) {
            return back()->with('error', 'Stok habis');
        }

        if ($request->qty > $variant->stock) {
            return back()->with('error', 'Jumlah melebihi stok');
        }

        // 🔒 RESET SESSION (anti tab lama / race)
        session()->forget('buy_now');

        // ✅ SIMPAN SESSION DENGAN HARDENING
        session([
            'buy_now' => [
                'variant_id' => $variant->id,
                'qty' => (int) $request->qty,

                // 🔥 optional tapi bagus (anti manipulasi ringan)
                'price_snapshot' => $variant->price,
                'branch_id' => $variant->branch_id,

                // 🔒 expiry
                'expired_at' => now()->addMinutes(10)->timestamp
            ]
        ]);

        return redirect()->route('customer.checkout');
    }
}
