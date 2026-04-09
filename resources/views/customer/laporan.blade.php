@extends('layouts.customer')

@section('customer')
    <section class="laporan-page">
        <div class="container py-5">

            {{-- ================= HEADER ================= --}}
            <div class="laporan-header mb-5">
                <div>
                    <h2 class="page-title">Laporan Belanja Saya</h2>
                    <p class="page-subtitle">
                        Pantau riwayat transaksi dan total pengeluaran Anda
                    </p>
                </div>
            </div>

            {{-- ================= FILTER ================= --}}
            <div class="glass-card filter-card mb-4">
                <form method="GET" action="{{ route('customer.laporan') }}" class="row g-3 align-items-end">

                    {{-- DATE FROM --}}
                    <div class="col-md-3">
                        <label class="filter-label">Dari</label>
                        <input type="date" name="date_from" class="form-control modern-input"
                            value="{{ request('date_from') }}">
                    </div>

                    {{-- DATE TO --}}
                    <div class="col-md-3">
                        <label class="filter-label">Sampai</label>
                        <input type="date" name="date_to" class="form-control modern-input"
                            value="{{ request('date_to') }}">
                    </div>

                    {{-- SEARCH --}}
                    <div class="col-md-3">
                        <label class="filter-label">Search</label>
                        <input type="text" name="search" class="form-control modern-input"
                            placeholder="Invoice / Cabang..." value="{{ request('search') }}">
                    </div>

                    {{-- STATUS --}}
                    <div class="col-md-3">
                        <label class="filter-label">Status</label>
                        <select name="status" class="form-select modern-input">

                            <option value="">Semua</option>

                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="process" {{ request('status') == 'process' ? 'selected' : '' }}>
                                Process
                            </option>

                            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>
                                Done
                            </option>

                            <option value="cancel" {{ request('status') == 'cancel' ? 'selected' : '' }}>
                                Cancel
                            </option>

                        </select>
                    </div>

                    {{-- BUTTON FILTER --}}
                    <div class="col-md-6">
                        <button type="submit" class="btn-filter w-100">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Terapkan Filter
                        </button>
                    </div>

                    {{-- EXPORT PDF (ikut filter aktif) --}}
                    <div class="col-md-6">
                        <a href="{{ route('customer.laporan.export', request()->query()) }}"
                            class="btn-export w-100 text-center d-block">

                            <i class="fa-solid fa-file-pdf"></i>
                            Export PDF
                        </a>
                    </div>

                </form>
            </div>

            {{-- ================= STATISTICS ================= --}}
            <div class="row g-4 mb-5">

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon bg-blue">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <p>Total Order</p>
                        <h4>{{ $totalOrders }}</h4>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon bg-green">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <p>Order Selesai</p>
                        <h4>{{ $totalDone }}</h4>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon bg-orange">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <p>Sedang Diproses</p>
                        <h4>{{ $totalProcess }}</h4>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card highlight">
                        <div class="stat-icon bg-purple">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <p>Total Belanja</p>
                        <h4>Rp {{ number_format($totalSpent, 0, ',', '.') }}</h4>
                    </div>
                </div>

            </div>

            {{-- ================= TABLE ================= --}}
            <div class="glass-card table-card">
                <div class="table-responsive">
                    <table class="modern-table w-100">
                        <thead>
                            <tr>
                                <th>#Order</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                @php
                                    $badge = match ($order->status) {
                                        'pending' => 'badge-pending',
                                        'process' => 'badge-process',
                                        'done' => 'badge-done',
                                        'cancel' => 'badge-cancel',
                                        default => 'badge-default',
                                    };
                                @endphp
                                <tr>
                                    <td class="fw-semibold">#{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td class="fw-bold">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $badge }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="empty-state">
                                        Belum ada transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection
@push('styles')
    <style>
        .laporan-page {
            background: radial-gradient(circle at top, #111827, #0b1220);
            min-height: 100vh;
        }

        .page-title {
            color: #fff;
            font-weight: 700;
        }

        .page-subtitle {
            color: #9ca3af;
            font-size: 14px;
        }

        .laporan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 22px;
            padding: 25px;
            backdrop-filter: blur(15px);
            transition: .3s;
        }

        .glass-card:hover {
            border-color: rgba(255, 255, 255, .2);
        }

        .filter-label {
            font-size: 12px;
            color: #9ca3af;
        }

        .modern-input {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            color: #fff;
            border-radius: 12px;
        }

        .modern-input:focus {
            border-color: #6366f1;
            box-shadow: none;
        }

        .stat-card {
            background: rgba(255, 255, 255, .05);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            transition: .3s;
            border: 1px solid rgba(255, 255, 255, .08);
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, .4);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 18px;
            color: #fff;
        }

        .bg-blue {
            background: #3b82f6;
        }

        .bg-green {
            background: #22c55e;
        }

        .bg-orange {
            background: #f59e0b;
        }

        .bg-purple {
            background: #8b5cf6;
        }

        .stat-card p {
            color: #9ca3af;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .stat-card h4 {
            color: #fff;
            font-weight: 700;
        }

        .stat-card.highlight {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }

        .btn-export {
            background: linear-gradient(135deg, #ef4444, #f87171);
            padding: 10px 18px;
            border-radius: 14px;
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            transition: .3s;
        }

        .btn-export:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, .4);
        }

        .btn-filter {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            padding: 10px;
            border-radius: 14px;
            color: #fff;
            font-weight: 600;
        }

        .modern-table thead {
            background: rgba(255, 255, 255, .05);
        }

        .modern-table th {
            font-size: 13px;
            color: #9ca3af;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        .modern-table td {
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, .05);
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pending {
            background: rgba(251, 191, 36, .2);
            color: #fbbf24;
        }

        .badge-process {
            background: rgba(59, 130, 246, .2);
            color: #3b82f6;
        }

        .badge-done {
            background: rgba(34, 197, 94, .2);
            color: #22c55e;
        }

        .badge-cancel {
            background: rgba(239, 68, 68, .2);
            color: #ef4444;
        }

        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: #9ca3af;
            font-size: 14px;
        }

        .empty-state i {
            display: block;
            font-size: 28px;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, .3);
        }

        .table-card {
            padding: 0;
            overflow: hidden;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table thead {
            background: rgba(255, 255, 255, .06);
        }

        .modern-table th {
            padding: 18px 20px;
            text-align: left;
            font-size: 13px;
            color: #9ca3af;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        .modern-table td {
            padding: 18px 20px;
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, .05);
            font-size: 14px;
        }

        .modern-table tbody tr {
            transition: .2s ease;
        }

        .modern-table tbody tr:hover {
            background: rgba(255, 255, 255, .04);
        }

        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: #9ca3af;
        }

        @media(max-width:768px) {
            .laporan-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush
