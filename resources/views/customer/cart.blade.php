@extends('layouts.customer')

@section('customer')
    <section class="td-page td-page--after-navbar" style="background:#0b1220;">
        <div class="container py-5">

            @if ($cart->items->count())
                @php $grandTotal = 0; @endphp

                <div class="row g-4">
                    <div class="col-lg-8">

                        @foreach ($cart->items as $item)
                            @php
                                $product = $item->variant?->product ?? null;
                                $subtotal = ($item->variant->price ?? 0) * $item->qty;
                                $grandTotal += $subtotal;
                                $imgPath = $product?->mainImage?->path
                                    ? asset('storage/' . $product->mainImage->path)
                                    : asset('images/no-image.png');
                            @endphp

                            <div class="td-cart-card mb-3 p-4 rounded-4 shadow-sm">
                                <div class="d-flex flex-column flex-lg-row align-items-start gap-4">

                                    {{-- LEFT SECTION --}}
                                    <div class="d-flex gap-3 flex-grow-1">

                                        {{-- IMAGE --}}
                                        <div class="td-cart-img">
                                            <img src="{{ $imgPath }}" alt="{{ $product?->name ?? 'Produk' }}"
                                                onerror="this.src='{{ asset('images/no-image.png') }}'; this.onerror=null;">
                                        </div>

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
                                                @if ($item->variant?->attributes)
                                                    {{ collect($item->variant->attributes)->map(fn($v, $k) => ucfirst($k) . ': ' . $v)->implode(' • ') }}
                                                @endif
                                            </div>

                                            {{-- PRICE --}}
                                            <div class="text-white fw-medium mb-2">
                                                Rp {{ number_format($item->variant->price ?? 0, 0, ',', '.') }}
                                            </div>

                                            {{-- QTY --}}
                                            <div class="td-qty-control">
                                                <button type="button" class="qty-minus">−</button>
                                                <input type="number" value="{{ $item->qty }}" min="1"
                                                    max="{{ $item->variant->stock }}"
                                                    data-price="{{ $item->variant->price }}" class="cart-qty">
                                                <button type="button" class="qty-plus">+</button>
                                            </div>

                                            <div class="td-stock small text-secondary mt-1">
                                                Stok: {{ $item->variant->stock }}
                                            </div>

                                        </div>
                                    </div>

                                    {{-- RIGHT SECTION --}}
                                    <div class="d-flex flex-column align-items-end justify-content-between gap-3">

                                        <div class="fw-bold fs-5 text-white cart-subtotal"
                                            data-subtotal="{{ $subtotal }}">
                                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                                        </div>

                                        <button type="button" class="td-delete btn-remove"
                                            data-url="{{ route('customer.cart.remove', $item->id) }}">
                                            <i class="fa-solid fa-trash"></i>
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

                            @auth
                                <a href="{{ route('customer.checkout') }}" class="btn btn-td w-100">
                                    <i class="fa-solid fa-bolt"></i>
                                    Beli Sekarang
                                </a>
                            @else
                                <button class="btn btn-td w-100" disabled style="opacity:0.6; cursor:not-allowed;">
                                    <i class="fa-solid fa-lock"></i>
                                    Login untuk Beli
                                </button>
                            @endauth

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
    </style>
@endpush
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const grandTotalEl = document.getElementById('grandTotal');

            function formatRupiah(number) {
                return 'Rp ' + Number(number).toLocaleString('id-ID');
            }

            function calculateGrandTotal() {
                let total = 0;
                document.querySelectorAll('.cart-subtotal').forEach(sub => {
                    total += Number(sub.dataset.subtotal || 0);
                });

                if (grandTotalEl) {
                    grandTotalEl.innerText = formatRupiah(total);
                }
            }

            function updateSubtotal(input) {
                const qty = Math.max(1, Number(input.value));
                const max = Number(input.max);
                const price = Number(input.dataset.price);

                let finalQty = qty;

                if (qty > max) {
                    finalQty = max;
                    input.value = max;

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stok tidak mencukupi',
                            text: 'Jumlah melebihi stok tersedia.',
                            confirmButtonColor: '#6366f1'
                        });
                    }
                }

                if (finalQty < 1) {
                    finalQty = 1;
                    input.value = 1;
                }

                const subtotal = finalQty * price;

                const card = input.closest('.td-cart-card');
                const subtotalEl = card.querySelector('.cart-subtotal');

                subtotalEl.dataset.subtotal = subtotal;
                subtotalEl.innerText = formatRupiah(subtotal);

                calculateGrandTotal();
            }

            // EVENT DELEGATION (lebih clean & scalable)
            document.addEventListener('click', function(e) {

                // QTY MINUS
                if (e.target.closest('.qty-minus')) {
                    const input = e.target.closest('.td-qty-control')
                        .querySelector('.cart-qty');
                    input.stepDown();
                    updateSubtotal(input);
                }

                // QTY PLUS
                if (e.target.closest('.qty-plus')) {
                    const input = e.target.closest('.td-qty-control')
                        .querySelector('.cart-qty');
                    input.stepUp();
                    updateSubtotal(input);
                }

                // DELETE
                if (e.target.closest('.btn-remove')) {

                    const button = e.target.closest('.btn-remove');
                    const url = button.dataset.url;

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Hapus item?',
                            text: 'Item akan dihapus dari keranjang.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                submitDelete(url);
                            }
                        });
                    } else {
                        if (confirm('Hapus item dari keranjang?')) {
                            submitDelete(url);
                        }
                    }
                }
            });

            // INPUT MANUAL QTY
            document.querySelectorAll('.cart-qty').forEach(input => {
                input.addEventListener('input', function() {
                    updateSubtotal(this);
                });
            });

            function submitDelete(url) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;

                document.body.appendChild(form);
                form.submit();
            }

        });
    </script>
@endpush
