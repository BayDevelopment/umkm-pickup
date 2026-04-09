<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @stack('styles')
</head>

<body class="td-layout">

    <header>
        @include('partials.customer.navbar')
    </header>

    <main class="customer-content">
        @yield('customer')
    </main>

    @include('partials.customer.footer')

    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            iconColor: '#fff',
            customClass: {
                popup: 'td-toast'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        @if ($errors->any())
            Toast.fire({
                icon: 'error',
                title: `{!! implode(' | ', $errors->all()) !!}`
            });
        @endif
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.0/echo.iife.js"></script>

    @if (isset($order))
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                const orderId = {{ $order->id }};

                const echo = new Echo({
                    broadcaster: 'pusher',
                    key: "{{ env('PUSHER_APP_KEY') }}",
                    cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                    forceTLS: true
                });

                // =========================
                // ORDER STATUS
                // =========================
                function getOrderStatusClass(status) {
                    switch (status) {
                        case "pending":
                            return "badge-pending";
                        case "process":
                            return "badge-process";
                        case "done":
                            return "badge-done";
                        case "cancel":
                            return "badge-cancel";
                        default:
                            return "badge-default";
                    }
                }

                function getOrderStatusText(status) {
                    switch (status) {
                        case "pending":
                            return "Pesanan Menunggu Konfirmasi";
                        case "process":
                            return "Pesanan Sedang Diproses";
                        case "done":
                            return "Pesanan Selesai";
                        case "cancel":
                            return "Pesanan Dibatalkan";
                        default:
                            return "Pesanan Menunggu Konfirmasi";
                    }
                }

                // =========================
                // PAYMENT STATUS
                // =========================
                function getPaymentStatusClass(status) {
                    switch (status) {
                        case "pending":
                            return "badge-pending";
                        case "paid":
                            return "badge-done";
                        case "rejected":
                            return "badge-cancel";
                        default:
                            return "badge-default";
                    }
                }

                function getPaymentStatusText(status) {
                    switch (status) {
                        case "pending":
                            return "Pembayaran Menunggu Verifikasi";
                        case "paid":
                            return "Pembayaran Berhasil";
                        case "rejected":
                            return "Pembayaran Ditolak";
                        default:
                            return "Pembayaran Menunggu Verifikasi";
                    }
                }

                // =========================
                // UPDATE BADGE
                // =========================
                function updateBadge(badgeId, textId, status, type = 'order') {
                    const badge = document.getElementById(badgeId);
                    const text = document.getElementById(textId);

                    if (!badge || !text) return;

                    badge.classList.remove(
                        "badge-pending",
                        "badge-process",
                        "badge-done",
                        "badge-cancel",
                        "badge-default"
                    );

                    if (type === 'order') {
                        badge.classList.add(getOrderStatusClass(status));
                        text.innerText = getOrderStatusText(status);
                    } else {
                        badge.classList.add(getPaymentStatusClass(status));
                        text.innerText = getPaymentStatusText(status);
                    }
                }

                // =========================
                // REALTIME
                // =========================
                echo.channel('order.' + orderId)
                    .listen('.OrderStatusUpdated', (e) => {

                        console.log("REALTIME MASUK:", e);

                        // normalisasi
                        e.status = (e.status || '').toLowerCase();
                        e.payment_status = (e.payment_status || '').toLowerCase();

                        // update order
                        updateBadge(
                            "order-status-badge",
                            "order-status-text",
                            e.status,
                            'order'
                        );

                        // update payment
                        updateBadge(
                            "payment-status-badge",
                            "payment-status-text",
                            e.payment_status,
                            'payment'
                        );

                        Toast.fire({
                            icon: 'info',
                            title: 'Status pesanan diperbarui'
                        });
                    });

            });
        </script>
    @endif
</body>

</html>
