@extends('layouts.customer')

@section('customer')
    <div class="container-fluid pt-5 pb-2">

        <!-- HEADER -->
        <div class="mb-3">
            <h3 class="fw-bold text-white mb-1">
                Halo, {{ Auth::user()->name }} 👋
            </h3>
            <p class="text-muted mb-0">
                Selamat datang di dashboard belanja kamu
            </p>
        </div>

        <!-- STAT CARDS -->
        <div class="row g-4 mb-4">

            <!-- TOTAL PRODUK -->
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('customer.product') }}" class="td-card-link text-decoration-none">
                    <div class="td-card p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-secondary small mb-1">Total Produk</p>
                                <h4 class="fw-bold text-white mb-0">
                                    {{ $AllProduct ?? 0 }}
                                </h4>
                            </div>
                            <div class="td-dashboard-icon bg-primary">
                                <i class="fa-solid fa-box"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- TOTAL PESANAN -->
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('customer.orders') }}" class="td-card-link text-decoration-none">
                    <div class="td-card p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-secondary small mb-1">Pesanan Saya</p>
                                <h4 class="fw-bold text-white mb-0">
                                    {{ $totalOrders ?? 0 }}
                                </h4>
                            </div>
                            <div class="td-dashboard-icon bg-info">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- DIPROSES -->
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('customer.orders', ['status' => 'process']) }}" class="td-card-link text-decoration-none">
                    <div class="td-card p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-secondary small mb-1">Diproses</p>
                                <h4 class="fw-bold text-white mb-0">
                                    {{ $orderProcess ?? 0 }}
                                </h4>
                            </div>
                            <div class="td-dashboard-icon bg-warning">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- SELESAI -->
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('customer.orders', ['status' => 'done']) }}" class="td-card-link text-decoration-none">
                    <div class="td-card p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-secondary small mb-1">Selesai</p>
                                <h4 class="fw-bold text-white mb-0">
                                    {{ $orderDone ?? 0 }}
                                </h4>
                            </div>
                            <div class="td-dashboard-icon bg-success">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>


        <!-- CHART + STATUS -->
        <div class="row g-4">

            <!-- CHART -->
            <div class="col-lg-8">
                <div class="td-card p-4 chart-card">
                    <h5 class="fw-bold text-white mb-4">
                        Statistik Pesanan
                    </h5>

                    <canvas id="orderChart"></canvas>

                </div>
            </div>


            <!-- STATUS AKUN -->
            <div class="col-lg-4">
                <div class="td-card p-4 chart-card">

                    <h5 class="fw-bold text-white mb-4">
                        Status Akun
                    </h5>

                    @php
                        $profileComplete = Auth::user()->name && Auth::user()->email;
                    @endphp

                    <!-- EMAIL -->
                    <div class="td-status-card mb-3">
                        <div class="td-status-icon {{ Auth::user()->email_verified_at ? 'success' : 'danger' }}">
                            <i
                                class="fa-solid {{ Auth::user()->email_verified_at ? 'fa-envelope-circle-check' : 'fa-envelope-open-text' }}"></i>
                        </div>

                        <div>
                            <div class="fw-semibold text-white">
                                Email
                            </div>
                            <div class="small {{ Auth::user()->email_verified_at ? 'text-success' : 'text-danger' }}">
                                {{ Auth::user()->email_verified_at ? 'Sudah diverifikasi' : 'Belum diverifikasi' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>
@endsection


@push('styles')
    <style>
        .td-dashboard-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #fff;
        }

        .td-status-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .td-status-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .info {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        /* tinggi card  */
        .chart-card {
            height: auto;
            min-height: unset;
        }

        .chart-card canvas {
            max-height: 220px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('orderChart');

        new Chart(ctx, {

            type: 'doughnut',

            data: {

                labels: [
                    'Diproses',
                    'Selesai'
                ],

                datasets: [{

                    data: [
                        {{ $orderProcess ?? 0 }},
                        {{ $orderDone ?? 0 }}
                    ],

                    borderWidth: 0

                }]

            },

            options: {

                responsive: true,

                plugins: {

                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    }

                }

            }

        });
    </script>
@endpush
