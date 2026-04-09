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

        $query = OrderModel::with(['paymentMethod', 'branch'])
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
            'items.variant.product',
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

        // Hanya boleh upload kalau pending
        if ($order->payment_status !== 'pending') {
            return back()->withErrors(['payment' => 'Bukti sudah diupload atau pesanan tidak bisa diubah lagi.']);
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048', // naikkan ke 2MB, lebih nyaman
        ]);

        try {
            // Hapus bukti lama kalau ada
            if ($order->payment_proof && Storage::disk('public')->exists($order->payment_proof)) {
                Storage::disk('public')->delete($order->payment_proof);
            }

            $path = $request->file('payment_proof')->store('payment-proofs', 'public');

            $order->update([
                'payment_proof'   => $path,
                'payment_status'  => 'pending',
            ]);

            return back()->with('success', 'Bukti transfer berhasil diupload. Admin akan segera memverifikasi.');
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Gagal upload bukti: ' . $e->getMessage()]);
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
