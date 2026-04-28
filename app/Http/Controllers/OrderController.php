<?php

namespace App\Http\Controllers;

use App\Models\OrderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ORDER LIST (Pesanan Saya)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $allowedStatuses = ['pending', 'process', 'done', 'cancel'];

        $query = OrderModel::with([
            'paymentMethod',
            'branch',
            'items.variant.product.umkm', // ✅ ambil umkm via product
        ])
            ->where('user_id', Auth::id());

        // Filter status hanya jika valid
        if ($request->filled('status')) {

            $query->when(
                in_array($request->status, $allowedStatuses),
                fn($q) => $q->where('status', $request->status)
            )->when(
                !in_array($request->status, $allowedStatuses),
                fn($q) => $q->whereRaw('1 = 0')
            );
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        // ✅ TAMBAHKAN INI
        if ($request->ajax()) {

            return response()->json([
                'orders' => $orders->items()
            ]);
        }

        // return normal view
        return view('customer.order', compact('orders'))
            ->with([
                'title'   => 'Pesanan Saya | Trendora',
                'navlink' => 'Pesanan Saya',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER DETAIL
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, OrderModel $order)
    {
        $this->authorizeOrder($order);

        $order->load([
            'items.variant.product.umkm', // ✅
            'items.variant.product.mainImage', // ✅
            'items.variant.branch', // ✅
            'paymentMethod',
            'branch',
        ]);

        // ✅ TAMBAHKAN INI (untuk realtime tanpa reload)
        if ($request->ajax()) {

            return response()->json([
                'id' => $order->id,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'updated_at' => $order->updated_at->format('Y-m-d H:i:s'),
            ]);
        }

        // return view normal
        return view('customer.view-order', [
            'title'   => 'Detail Pesanan #' . $order->id,
            'navlink' => 'Detail Pesanan',
            'order'   => $order,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPLOAD BUKTI TRANSFER
    |--------------------------------------------------------------------------
    */
    public function uploadProof(Request $request, OrderModel $order)
    {
        $this->authorizeOrder($order);

        // ❗ hanya boleh upload jika masih pending
        if ($order->payment_status !== 'pending') {
            return back()->withErrors([
                'payment' => 'Pesanan tidak bisa diubah lagi.'
            ]);
        }

        // ❗ cegah upload ulang
        if ($order->payment_proof) {
            return back()->withErrors([
                'payment' => 'Bukti transfer sudah diupload dan tidak dapat diganti.'
            ]);
        }

        // ✅ VALIDASI
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:1024',
        ], [
            'payment_proof.required' => 'Bukti transfer wajib diupload.',
            'payment_proof.image' => 'File harus berupa gambar.',
            'payment_proof.mimes' => 'Format harus JPG atau PNG.',
            'payment_proof.max' => 'Ukuran maksimal 1MB.',
        ]);

        try {

            // 🔥 simpan file (tanpa hapus lama)
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');

            // 🔥 update order
            $order->update([
                'payment_proof'  => $path,
                'payment_status' => 'pending', // tetap pending → menunggu admin
            ]);

            return back()->with('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi admin.');
        } catch (\Exception $e) {

            return back()->withErrors([
                'payment' => 'Gagal upload bukti: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper: Authorize order milik user
     */
    private function authorizeOrder(OrderModel $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Ini bukan pesanan Anda.');
        }
    }

    // reload order
    public function statusAll()
    {
        $orders = OrderModel::where('user_id', Auth::id())
            ->select('id', 'status')
            ->get();

        return response()->json($orders);
    }
}
