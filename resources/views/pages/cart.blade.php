@extends('layouts.app')

@section('content')
    <section class="td-page td-page--after-navbar" style="background:#0b1220;">
        <div class="container py-5">

            @if ($cart->items->count())
                @php $grandTotal = 0; @endphp

                <div class="row g-4">
                    <div class="col-lg-8">

                        @foreach ($cart->items as $item)
                            @php
                                $product = $item->variant?->product ?? null;
                                $imageUrl = asset('images/no-image.png');
                                $subtotal = ($item->variant->price ?? 0) * $item->qty;
                                $grandTotal += $subtotal;

                                if ($product && $product->mainImage?->path) {
                                    $imageUrl = asset('storage/' . ltrim($product->mainImage->path, '/'));
                                }
                            @endphp

                            <div class="td-cart-card mb-3 p-4 rounded-4 shadow-sm">
                                <div class="d-flex flex-column flex-lg-row align-items-start gap-4">

                                    {{-- LEFT SECTION --}}
                                    <div class="d-flex gap-3 flex-grow-1">

                                        {{-- IMAGE --}}
                                        <div class="td-cart-img">
                                            <img src="{{ $imageUrl }}" alt="{{ $product?->name ?? 'Produk' }}"
                                                class="img-fluid rounded-3" loading="lazy"
                                                onerror="this.src='{{ asset('images/no-image.png') }}'; this.onerror=null;">
                                        </div>

                                        {{-- INFO --}}
                                        {{-- INFO --}}
                                        <div class="flex-grow-1">

                                            {{-- UMKM & Branch --}}
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                @if ($product?->umkm)
                                                    <span class="badge px-2 py-1"
                                                        style="background: rgba(99,102,241,0.2); color: #a5b4fc; font-size: 0.7rem; border: 1px solid rgba(99,102,241,0.4);">
                                                        <i class="fa-solid fa-store me-1"></i>{{ $product->umkm->name }}
                                                    </span>
                                                @endif

                                                @if ($product?->umkm && $item->variant?->branch)
                                                    <span style="color: #475569;">|</span>
                                                @endif

                                                @if ($item->variant?->branch)
                                                    <span class="badge px-2 py-1"
                                                        style="background: rgba(16,185,129,0.15); color: #6ee7b7; font-size: 0.7rem; border: 1px solid rgba(16,185,129,0.3);">
                                                        <i
                                                            class="fa-solid fa-location-dot me-1"></i>{{ $item->variant->branch->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            <h6 class="fw-semibold text-white mb-1">
                                                {{ $product?->name ?? '-' }}
                                            </h6>

                                            {{-- ATTRIBUTES --}}
                                            <div class="text-secondary small mb-2">
                                                @if ($item->variant->attributes)
                                                    {{ collect($item->variant->attributes)->map(fn($v, $k) => ucfirst($k) . ': ' . $v)->implode(' • ') }}
                                                @endif
                                            </div>

                                            {{-- PRICE --}}
                                            <div class="text-white fw-medium mb-2">
                                                Rp {{ number_format($item->variant->price ?? 0, 0, ',', '.') }}
                                            </div>

                                            {{-- QTY --}}
                                            <div class="td-qty-control">
                                                <button type="button" onclick="qtyMinus(this)">−</button>
                                                <input type="number" value="{{ $item->qty }}" min="1"
                                                    max="{{ $item->variant->stock }}"
                                                    data-price="{{ $item->variant->price }}" class="cart-qty">
                                                <button type="button" onclick="qtyPlus(this)">+</button>
                                            </div>

                                            <div class="td-stock small text-secondary mt-1">
                                                Stok: {{ $item->variant->stock }}
                                            </div>

                                        </div>
                                    </div>

                                    {{-- RIGHT SECTION --}}
                                    <div
                                        class="d-flex flex-row flex-sm-column align-items-center align-items-sm-end justify-content-between gap-3 mt-3 mt-sm-0">

                                        <div class="fw-bold fs-5 text-white cart-subtotal text-nowrap me-3 me-sm-0"
                                            data-subtotal="{{ $subtotal }}">
                                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                                        </div>

                                        <button type="button"
                                            class="td-delete btn-remove rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                            data-id="{{ $item->id }}" style="width:42px;height:42px;min-width:42px;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>

                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                    {{-- Summary --}}
                    <div class="col-lg-4">
                        <div class="p-4 rounded" style="background:rgba(255,255,255,.05);">

                            <h5 class="text-white mb-3">Ringkasan Belanja</h5>

                            <div class="d-flex justify-content-between mb-2 text-white">
                                <span>Total</span>
                                <strong id="grandTotal">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </strong>
                            </div>

                            <a href="{{ route('login') }}" class="btn btn-td w-100">
                                Login untuk Checkout
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa-solid fa-cart-shopping fa-3x mb-3" style="color:rgba(255,255,255,.3);"></i>
                    <h5 class="text-white">Keranjang Kosong</h5>
                    <p style="color:#aaa;">Yuk mulai belanja dan temukan produk terbaik!</p>
                    <a href="{{ url('/produk') }}" class="btn btn-td mt-3">Lihat Produk</a>
                </div>
            @endif

        </div>
    </section>
@endsection
@push('styles')
    <style>
        .td-cart-card {
            background: rgba(255, 255, 255, .05);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .08);
            transition: all .2s ease;
        }

        .td-cart-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, .15);
        }

        .td-cart-img img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
        }

        .td-qty-control {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, .08);
            border-radius: 50px;
            padding: 4px;
        }

        .td-qty-control button {
            background: none;
            border: none;
            color: #fff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            transition: .2s;
        }

        .td-qty-control button:hover {
            background: rgba(255, 255, 255, .15);
        }

        .td-qty-control input {
            width: 50px;
            text-align: center;
            background: transparent;
            border: none;
            color: #fff;
            outline: none;
        }

        .td-delete {
            background: rgba(239, 68, 68, .15);
            border: none;
            color: #ef4444;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            transition: .2s;
        }

        .td-delete:hover {
            background: #ef4444;
            color: #fff;
        }

        /* Perbaikan untuk mobile */
        .td-cart-card {
            border-radius: 16px;
            background: rgba(255, 255, 255, .05);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .08);
            transition: all .25s ease;
        }

        .td-cart-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, .3);
        }

        /* Gambar lebih besar di mobile */
        .td-cart-img {
            width: 90px;
            height: 90px;
            flex-shrink: 0;
            overflow: hidden;
            border-radius: 12px;
            background: linear-gradient(135deg, #1e1b4b, #0f172a);
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, .4);
        }

        .td-cart-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s ease;
        }

        .td-cart-card:hover .td-cart-img img {
            transform: scale(1.08);
        }

        /* Qty Control lebih besar & mudah ditekan */
        .td-qty-control {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, .08);
            border-radius: 50px;
            padding: 6px 8px;
            gap: 8px;
        }

        .td-qty-btn {
            background: none;
            border: none;
            color: #fff;
            width: 40px;
            height: 40px;
            font-size: 1.3rem;
            border-radius: 50%;
            transition: .2s;
        }

        .td-qty-btn:hover {
            background: rgba(255, 255, 255, .15);
        }

        .cart-qty {
            width: 60px;
            text-align: center;
            background: transparent;
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            outline: none;
        }

        /* Tombol hapus lebih besar & mudah ditekan */
        .td-delete {
            background: rgba(239, 68, 68, .2);
            border: none;
            color: #ef4444;
            width: 42px;
            height: 42px;
            font-size: 1.2rem;
            border-radius: 50%;
            transition: all .3s;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(239, 68, 68, .2);
        }

        .td-delete:hover {
            background: #ef4444;
            color: #fff;
            transform: scale(1.1);
        }

        /* Ringkasan belanja di mobile jadi fixed bottom */
        @media (max-width: 991px) {
            .summary-card {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                border-radius: 20px 20px 0 0;
                z-index: 1000;
                box-shadow: 0 -10px 30px rgba(0, 0, 0, .6);
                padding: 16px;
                background: rgba(15, 23, 42, .95);
                backdrop-filter: blur(10px);
                border-top: 1px solid rgba(255, 255, 255, .1);
            }

            .summary-card .btn-td {
                padding: 14px;
                font-size: 1.1rem;
            }

            .td-cart-card {
                margin-bottom: 1.5rem !important;
            }

            .td-cart-img {
                width: 80px;
                height: 80px;
            }
        }
    </style>
@endpush
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const qtyInputs = document.querySelectorAll('.cart-qty');
            const grandTotalEl = document.getElementById('grandTotal');

            function formatRupiah(number) {
                return 'Rp ' + number.toLocaleString('id-ID');
            }

            function calculateGrandTotal() {
                let total = 0;
                document.querySelectorAll('.cart-subtotal').forEach(sub => {
                    total += parseInt(sub.dataset.subtotal);
                });
                grandTotalEl.innerText = formatRupiah(total);
            }

            qtyInputs.forEach(input => {

                input.addEventListener('input', function() {

                    let qty = parseInt(this.value);
                    const max = parseInt(this.max);
                    const price = parseInt(this.dataset.price);

                    if (qty > max) {
                        qty = max;
                        this.value = max;

                        Swal.fire({
                            icon: 'warning',
                            title: 'Stok tidak mencukupi',
                            text: 'Jumlah melebihi stok tersedia.',
                            confirmButtonColor: '#6366f1'
                        });
                    }

                    if (qty < 1) {
                        qty = 1;
                        this.value = 1;
                    }

                    const subtotal = qty * price;
                    const subtotalEl = this.closest('.row').querySelector('.cart-subtotal');

                    subtotalEl.dataset.subtotal = subtotal;
                    subtotalEl.innerText = formatRupiah(subtotal);

                    calculateGrandTotal();
                });
            });

            // DELETE CONFIRMATION
            document.querySelectorAll('.btn-remove').forEach(button => {

                button.addEventListener('click', function() {

                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Hapus item?',
                        text: "Item akan dihapus dari keranjang.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {

                        if (result.isConfirmed) {

                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/cart/remove/${id}`;

                            const csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';

                            const method = document.createElement('input');
                            method.type = 'hidden';
                            method.name = '_method';
                            method.value = 'DELETE';

                            form.appendChild(csrf);
                            form.appendChild(method);

                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

        });

        function qtyMinus(btn) {
            const input = btn.parentElement.querySelector('.cart-qty');
            input.stepDown();
            input.dispatchEvent(new Event('input'));
        }

        function qtyPlus(btn) {
            const input = btn.parentElement.querySelector('.cart-qty');
            input.stepUp();
            input.dispatchEvent(new Event('input'));
        }
    </script>
@endpush
