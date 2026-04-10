<?php

namespace App\Http\Controllers;

use App\Models\OrderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = OrderModel::query()
            ->where('user_id', Auth::id());

        // ================= FILTER =================

        // 📅 Filter tanggal (FIX: samakan dengan view)
        if ($request->date_from) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // 🔍 Search (AMAN: pakai grouping)
        if ($request->search) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('invoice', 'like', '%' . $request->search . '%')
                    ->orWhereHas('branch', function ($q2) use ($request) {
                        $q2->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // 📊 Status (FIX: semua status bisa)
        if ($request->status) {
            $baseQuery->where('status', $request->status);
        }

        // ================= DATA =================

        $orders = (clone $baseQuery)
            ->latest()
            ->paginate(10)
            ->withQueryString(); // 🔥 biar filter tetap nempel

        // ================= STATISTIK (OPTIMIZED) =================

        $totalOrders  = (clone $baseQuery)->count();
        $totalDone    = (clone $baseQuery)->where('status', 'done')->count();
        $totalProcess = (clone $baseQuery)->where('status', 'process')->count();
        $totalPending = (clone $baseQuery)->where('status', 'pending')->count();
        $totalSpent   = (clone $baseQuery)->where('status', 'done')->sum('total_price');

        return view('customer.laporan', compact(
            'orders',
            'totalOrders',
            'totalDone',
            'totalProcess',
            'totalPending',
            'totalSpent'
        ), [
            'title' => 'Laporan Belanja | Trendora',
            'navlink' => 'laporan',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $query = OrderModel::with(['paymentMethod', 'branch'])
            ->where('user_id', Auth::id());

        // FILTER STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // FILTER DATE FROM
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // FILTER DATE TO
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // SEARCH
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {

                $q->where('invoice_number', 'like', "%{$request->search}%")
                    ->orWhereHas('branch', function ($branch) use ($request) {
                        $branch->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        $orders = $query->latest()->get();

        $totalOrders = $orders->count();
        $totalAmount = $orders->sum('total_price');

        $pdf = Pdf::loadView('customer.laporan-pdf', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'totalAmount' => $totalAmount,
            'filters' => $request->all(),
            'exportedAt' => now()
        ])->setPaper('A4', 'portrait');

        return $pdf->download('Laporan-Pesanan-Trendora.pdf');
    }
}
