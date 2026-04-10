<?php

namespace App\Http\Controllers;

use App\Models\CartItemModel;
use App\Models\CartModel;
use App\Models\ProductVariantModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getCart()
    {
        if (Auth::check()) {
            return CartModel::firstOrCreate([
                'user_id' => Auth::id(),
            ]);
        }

        return CartModel::firstOrCreate([
            'session_id' => session()->getId(),
        ]);
    }

    /*
    |------------------------------------------------------------------
    | VIEW CART
    |------------------------------------------------------------------
    */
    public function index()
    {
        $cart = $this->getCart()->load(['items.variant.product']);

        $viewPrefix = 'pages';

        if (Auth::check() && Auth::user()->role === 'customer') {
            $viewPrefix = 'customer';
        }

        return view($viewPrefix . '.cart', [
            'title'   => 'Keranjang | Trendora',
            'navlink' => 'cart',
            'cart'    => $cart,
        ]);
    }

    public function indexCustomer()
    {
        session()->forget('buy_now');

        $cart = $this->getCart()->load(['items.variant.product']);

        return view('customer.cart', [
            'title'   => 'Keranjang | Trendora',
            'navlink' => 'cart',
            'cart'    => $cart,
        ]);
    }

    /*
    |------------------------------------------------------------------
    | CORE ADD TO CART (DIGUNAKAN SEMUA)
    |------------------------------------------------------------------
    */
    private function handleAddToCart($variantId, $qty)
    {
        // 🔒 ambil dari DB (source of truth)
        $variant = ProductVariantModel::with(['product', 'branch'])
            ->find($variantId);

        if (!$variant) {
            return ['error' => 'Variant tidak valid.'];
        }

        // 🔒 validasi produk
        if (!$variant->product || !$variant->product->is_active) {
            return ['error' => 'Produk tidak tersedia.'];
        }

        // 🔒 validasi cabang
        if (!$variant->branch || !$variant->branch->is_active) {
            return ['error' => 'Cabang tidak tersedia.'];
        }

        // 🔒 validasi stock
        if ($variant->stock <= 0) {
            return ['error' => 'Stok habis.'];
        }

        if ($qty > $variant->stock) {
            return ['error' => 'Jumlah melebihi stok tersedia.'];
        }

        $cart = $this->getCart();

        $item = CartItemModel::where('cart_id', $cart->id)
            ->where('variant_id', $variant->id)
            ->first();

        if ($item) {

            $newQty = $item->qty + $qty;

            if ($newQty > $variant->stock) {
                return ['error' => 'Jumlah melebihi stok tersedia.'];
            }

            $item->update([
                'qty' => $newQty
            ]);
        } else {

            CartItemModel::create([
                'cart_id'    => $cart->id,
                'variant_id' => $variant->id,
                'qty'        => $qty,
            ]);
        }

        return ['success' => true];
    }

    /*
    |------------------------------------------------------------------
    | ADD TO CART
    |------------------------------------------------------------------
    */
    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'qty'        => 'required|integer|min:1|max:100',
        ]);

        // 🔒 optional anti manipulasi
        $variant = ProductVariantModel::find($request->variant_id);

        if ($request->filled('color') && $request->color !== $variant->color) {
            return back()->with('error', 'Data warna tidak valid.');
        }

        if ($request->filled('size') && $request->size !== $variant->size) {
            return back()->with('error', 'Data ukuran tidak valid.');
        }

        $result = $this->handleAddToCart(
            $request->variant_id,
            (int) $request->qty
        );

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return redirect()->route('cart.index')
            ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function addCustomer(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'qty'        => 'required|integer|min:1|max:100',
        ]);

        $result = $this->handleAddToCart(
            $request->variant_id,
            (int) $request->qty
        );

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return redirect()->route('customer.cart.index')
            ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /*
    |------------------------------------------------------------------
    | UPDATE QTY
    |------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $cart = $this->getCart();

        $item = CartItemModel::where('cart_id', $cart->id)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'qty' => 'required|integer|min:1|max:100'
        ]);

        // 🔒 FIX NULL VARIANT
        if (!$item->variant) {
            return back()->with('error', 'Variant tidak valid.');
        }

        if ($request->qty > $item->variant->stock) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $item->update([
            'qty' => $request->qty
        ]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function updateCustomer(Request $request, CartItemModel $item)
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1|max:100',
        ]);

        $cart = $this->getCart();

        if ($item->cart_id !== $cart->id) {
            abort(403);
        }

        // 🔒 FIX NULL VARIANT
        if (!$item->variant) {
            return back()->with('error', 'Variant tidak valid.');
        }

        if ($validated['qty'] > $item->variant->stock) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $item->update([
            'qty' => $validated['qty'],
        ]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    /*
    |------------------------------------------------------------------
    | REMOVE ITEM
    |------------------------------------------------------------------
    */
    public function remove($id)
    {
        $cart = $this->getCart();

        $item = CartItemModel::where('cart_id', $cart->id)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function removeCustomer($id)
    {
        return $this->remove($id);
    }
}
