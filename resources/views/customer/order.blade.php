@extends('layouts.customer')

@section('customer')
    <section class="td-page td-page--after-navbar" style="background:#0b1220;">
        <div class="container">

            <h3 class="text-white mb-4">Pesanan Saya</h3>

            <div class="order-scroll">
                @forelse ($orders as $order)
                    @php
                        $statusColor = match ($order->status) {
                            'pending' => 'badge-pending',
                            'process' => 'badge-process',
                            'done' => 'badge-done',
                            'cancel' => 'badge-cancel',
                            default => 'badge-default',
                        };
                    @endphp

                    <div class="order-card">

                        <div class="order-left">
                            <div class="order-id">
                                Order #{{ $order->order_code }}
                            </div>

                            <div class="order-date">
                                {{ $order->created_at->format('d M Y H:i') }}
                            </div>

                            {{-- UMKM & Branch --}}
                            @php
                                // Ambil umkm dari item pertama
                                $umkm = $order->items->first()?->variant?->product?->umkm;
                            @endphp

                            {{-- UMKM & Branch --}}
                            <div class="d-flex align-items-center gap-2 mt-1 mb-2">
                                @if ($umkm)
                                    <span class="badge px-2 py-1"
                                        style="background: rgba(99,102,241,0.2); color: #a5b4fc; font-size: 0.7rem; border: 1px solid rgba(99,102,241,0.4);">
                                        <i class="fa-solid fa-store me-1"></i>{{ $umkm->name }}
                                    </span>
                                @endif

                                @if ($umkm && $order->branch)
                                    <span style="color: #475569;">|</span>
                                @endif

                                @if ($order->branch)
                                    <span class="badge px-2 py-1"
                                        style="background: rgba(16,185,129,0.15); color: #6ee7b7; font-size: 0.7rem; border: 1px solid rgba(16,185,129,0.3);">
                                        <i class="fa-solid fa-location-dot me-1"></i>{{ $order->branch->name }}
                                    </span>
                                @endif
                            </div>

                            <span id="order-status-{{ $order->id }}" class="order-badge {{ $statusColor }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <div class="order-right">
                            <div class="order-price">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </div>

                            <a href="{{ route('customer.orders.show', $order->id) }}"
                                class="btn-modern d-inline-flex align-items-center gap-2 text-decoration-none">
                                <i class="fa-solid fa-eye"></i>
                                <span>Lihat Detail</span>
                            </a>
                        </div>

                    </div>

                @empty
                    <div class="empty-state">
                        <i class="fa-solid fa-box-open"></i>
                        <h5>Belum ada pesanan</h5>
                        <p>Yuk mulai belanja sekarang 🔥</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-3">
                {{ $orders->links() }}
            </div>

        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* scroll */
        .order-scroll {
            max-height: 350px;
            overflow-y: auto;
            padding-right: 6px;
        }

        /* scrollbar modern */
        .order-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .order-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .order-scroll::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.4);
            border-radius: 10px;
        }

        .order-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(139, 92, 246, 0.7);
        }

        /* ================= PAGE BACKGROUND ================= */
        .td-page {
            background:
                radial-gradient(circle at 20% 0%, #1e293b, transparent 40%),
                radial-gradient(circle at 80% 100%, #312e81, transparent 40%),
                linear-gradient(135deg, #0b1220, #020617);
            min-height: 100vh;
        }

        /* ================= PAGE TITLE ================= */
        h3 {
            font-weight: 700;
            letter-spacing: .3px;
        }

        /* ================= ORDER CARD ================= */
        .order-card {

            background: rgba(255, 255, 255, 0.04);

            border: 1px solid rgba(255, 255, 255, 0.08);

            border-radius: 20px;

            padding: 22px 24px;

            margin-bottom: 18px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            backdrop-filter: blur(14px);

            transition: all .35s ease;

            position: relative;

            overflow: hidden;
        }

        /* subtle glow */
        .order-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg,
                    transparent,
                    rgba(99, 102, 241, .15),
                    transparent);
            opacity: 0;
            transition: opacity .35s ease;
            pointer-events: none;
            /* FIX */
        }

        .order-card:hover {

            transform: translateY(-6px);

            border-color: rgba(99, 102, 241, .4);

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .5),
                0 0 25px rgba(99, 102, 241, .15);
        }

        .order-card:hover::before {
            opacity: 1;
        }


        /* ================= LEFT SECTION ================= */
        .order-left {

            display: flex;

            flex-direction: column;

            gap: 6px;
        }

        .order-id {

            font-size: 17px;

            font-weight: 700;

            color: #fff;
        }

        .order-date {

            font-size: 13px;

            color: #94a3b8;
        }

        .order-branch {

            font-size: 13px;

            color: #a5b4fc;

            font-weight: 500;
        }


        /* ================= RIGHT SECTION ================= */
        .order-right {

            text-align: right;

            display: flex;

            flex-direction: column;

            align-items: flex-end;

            gap: 10px;
        }

        .order-price {

            font-size: 22px;

            font-weight: 800;

            background: linear-gradient(135deg, #ffffff, #a5b4fc);

            -webkit-background-clip: text;

            -webkit-text-fill-color: transparent;
        }


        /* ================= STATUS BADGE ================= */
        .order-badge {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding: 6px 14px;

            border-radius: 999px;

            font-size: 12px;

            font-weight: 600;

            width: fit-content;

            letter-spacing: .4px;
        }

        /* Pending */
        .badge-pending {

            background: rgba(251, 191, 36, .12);

            color: #fbbf24;

            border: 1px solid rgba(251, 191, 36, .25);
        }

        /* Process */
        .badge-process {

            background: rgba(59, 130, 246, .12);

            color: #60a5fa;

            border: 1px solid rgba(59, 130, 246, .25);
        }

        /* Done */
        .badge-done {

            background: rgba(34, 197, 94, .12);

            color: #22c55e;

            border: 1px solid rgba(34, 197, 94, .25);
        }

        /* Cancel */
        .badge-cancel {

            background: rgba(239, 68, 68, .12);

            color: #ef4444;

            border: 1px solid rgba(239, 68, 68, .25);
        }


        /* ================= MODERN BUTTON ================= */
        .btn-modern {

            background: linear-gradient(135deg, #6366f1, #8b5cf6);

            border: none;

            border-radius: 12px;

            padding: 9px 16px;

            color: #fff !important;

            font-weight: 600;

            font-size: 14px;

            transition: all .3s ease;

            box-shadow: 0 6px 20px rgba(99, 102, 241, .25);
        }

        .btn-modern:hover {

            transform: translateY(-3px);

            box-shadow: 0 12px 30px rgba(99, 102, 241, .5);

            color: #fff !important;
        }


        /* ================= EMPTY STATE ================= */
        .empty-state {

            text-align: center;

            padding: 100px 20px;

            background: rgba(255, 255, 255, .03);

            border-radius: 20px;

            border: 1px solid rgba(255, 255, 255, .06);

            backdrop-filter: blur(10px);
        }

        .empty-state i {

            font-size: 50px;

            margin-bottom: 20px;

            color: rgba(99, 102, 241, .5);
        }

        .empty-state h5 {

            color: #fff;

            font-weight: 600;

            margin-bottom: 8px;
        }

        .empty-state p {

            color: #94a3b8;
        }


        /* ================= PAGINATION ================= */
        .pagination {

            gap: 6px;
        }

        .pagination .page-link {

            background: rgba(255, 255, 255, .04);

            border: 1px solid rgba(255, 255, 255, .08);

            color: #cbd5e1;

            border-radius: 10px;

            padding: 6px 12px;
        }

        .pagination .page-link:hover {

            background: rgba(99, 102, 241, .2);

            border-color: rgba(99, 102, 241, .4);

            color: #fff;
        }

        .pagination .active .page-link {

            background: linear-gradient(135deg, #6366f1, #8b5cf6);

            border: none;

            color: #fff;
        }


        /* ================= MOBILE ================= */
        @media (max-width:768px) {

            .order-card {

                flex-direction: column;

                align-items: flex-start;

                gap: 14px;
            }

            .order-right {

                width: 100%;

                align-items: flex-start;

                text-align: left;
            }

            .order-price {

                font-size: 20px;
            }

        }

        .order-branch {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #c4b5fd;
            background: rgba(139, 92, 246, 0.10);
            border: 1px solid rgba(139, 92, 246, 0.25);
            padding: 4px 10px;
            border-radius: 999px;
            width: fit-content;
            margin-top: 4px;
            transition: all 0.2s ease;
        }

        .order-branch:hover {
            background: rgba(139, 92, 246, 0.18);
            border-color: rgba(139, 92, 246, 0.4);
        }

        .branch-icon {
            font-size: 12px;
            color: #a78bfa;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // =========================
            // HELPER
            // =========================
            function getStatusClass(status) {
                switch (status) {
                    case 'pending':
                        return 'badge-pending';
                    case 'process':
                        return 'badge-process';
                    case 'done':
                        return 'badge-done';
                    case 'cancel':
                        return 'badge-cancel';
                    default:
                        return 'badge-default';
                }
            }

            function formatStatusText(status) {
                return status.charAt(0).toUpperCase() + status.slice(1);
            }

            function updateBadge(id, status) {
                const badge = document.getElementById("order-status-" + id);
                if (!badge) return;

                badge.classList.remove(
                    "badge-pending",
                    "badge-process",
                    "badge-done",
                    "badge-cancel",
                    "badge-default"
                );

                badge.classList.add(getStatusClass(status));
                badge.innerText = formatStatusText(status);
            }

            // =========================
            // 🔥 WEBSOCKET (PRIMARY)
            // =========================
            const userId = {{ auth()->id() }};

            Pusher.logToConsole = true;

            const echo = new Echo({
                broadcaster: 'pusher',
                key: "{{ env('PUSHER_APP_KEY') }}",
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                forceTLS: true
            });

            echo.channel('user.' + userId)
                .listen('OrderStatusUpdated', (e) => {

                    console.log("REALTIME LIST:", e);

                    updateBadge(e.id, e.status);

                    Toast.fire({
                        icon: 'info',
                        title: 'Status pesanan diperbarui'
                    });
                });

            // =========================
            // 🔁 POLLING (FALLBACK)
            // =========================
            let isFetching = false;

            async function updateOrderStatus() {
                if (isFetching) return;

                isFetching = true;

                try {
                    const response = await fetch(
                        "{{ route('customer.orders.status.all') }}", {
                            headers: {
                                "Accept": "application/json",
                                "X-Requested-With": "XMLHttpRequest"
                            }
                        }
                    );

                    if (!response.ok) return;

                    const orders = await response.json();

                    orders.forEach(order => {
                        updateBadge(order.id, order.status);
                    });

                } catch (e) {
                    console.warn("Polling gagal:", e);
                } finally {
                    isFetching = false;
                }
            }

            // jalankan sekali
            updateOrderStatus();

            // fallback tiap 10 detik (tidak terlalu sering)
            setInterval(updateOrderStatus, 10000);

        });
    </script>
@endpush
