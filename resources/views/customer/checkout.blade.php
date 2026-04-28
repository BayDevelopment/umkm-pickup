@extends('layouts.customer')

@section('customer')
    <section class="td-page td-page--after-navbar checkout-page">
        <div class="container py-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="checkout-title">Checkout</h3>
                @if (isset($isBuyNow) && $isBuyNow)
                    <a href="{{ url()->previous() }}" class="btn btn-back">← Kembali ke Produk</a>
                @else
                    <a href="{{ route('customer.cart.index') }}" class="btn btn-back">← Kembali ke Keranjang</a>
                @endif
            </div>

            @if ($errors->any())
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row g-4">

                {{-- ================= LEFT ================= --}}
                <div class="col-lg-8">
                    <div class="glass-card p-4">

                        <h5 class="section-title mb-4">Ringkasan Item</h5>

                        @if (isset($isBuyNow) && $isBuyNow)
                            @php
                                $variant = $buyNowItem->variant;
                                $product = $variant?->product;
                                $imgPath = $product?->mainImage?->path
                                    ? asset('storage/' . $product->mainImage->path)
                                    : asset('images/no-image.png');
                                $price = (int) ($variant?->price ?? 0);
                                $qty = (int) ($buyNowItem->qty ?? 0);
                                $subtotal = $price * $qty;
                            @endphp

                            <div class="checkout-item">

                                <div class="item-img">
                                    <img src="{{ $imgPath }}" alt="{{ $product?->name }}"
                                        onerror="this.src='{{ asset('images/no-image.png') }}'; this.onerror=null;">
                                </div>

                                <div class="item-info">

                                    {{-- UMKM & Branch --}}
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        @if ($product?->umkm)
                                            <span class="badge px-2 py-1"
                                                style="background: rgba(99,102,241,0.2); color: #a5b4fc; font-size: 0.65rem; border: 1px solid rgba(99,102,241,0.4);">
                                                <i class="fa-solid fa-store me-1"></i>{{ $product->umkm->name }}
                                            </span>
                                        @endif

                                        @if ($product?->umkm && $variant?->branch)
                                            <span style="color: #475569;">|</span>
                                        @endif

                                        @if ($variant?->branch)
                                            <span class="badge px-2 py-1"
                                                style="background: rgba(16,185,129,0.15); color: #6ee7b7; font-size: 0.65rem; border: 1px solid rgba(16,185,129,0.3);">
                                                <i class="fa-solid fa-location-dot me-1"></i>{{ $variant->branch->name }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="item-name">{{ $product?->name ?? '-' }}</div>

                                    {{-- Attributes --}}
                                    @if ($variant?->attributes)
                                        <div class="text-secondary small mb-1">
                                            {{ collect($variant->attributes)->map(fn($v, $k) => ucfirst($k) . ': ' . $v)->implode(' • ') }}
                                        </div>
                                    @endif

                                    <div class="item-price">
                                        Rp {{ number_format($price, 0, ',', '.') }} × {{ $qty }}
                                    </div>
                                </div>

                                <div class="item-subtotal">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </div>

                            </div>
                        @else
                            @foreach ($cart->items as $item)
                                @php
                                    $variant = $item->variant;
                                    $product = $variant?->product;
                                    $imgPath = $product?->mainImage?->path
                                        ? asset('storage/' . $product->mainImage->path)
                                        : asset('images/no-image.png');
                                    $price = (int) ($variant?->price ?? 0);
                                    $qty = (int) ($item->qty ?? 0);
                                    $subtotal = $price * $qty;
                                @endphp

                                <div class="checkout-item">

                                    <div class="item-img">
                                        <img src="{{ $imgPath }}" alt="{{ $product?->name }}"
                                            onerror="this.src='{{ asset('images/no-image.png') }}'; this.onerror=null;">
                                    </div>

                                    <div class="item-info">

                                        {{-- UMKM & Branch --}}
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            @if ($product?->umkm)
                                                <span class="badge px-2 py-1"
                                                    style="background: rgba(99,102,241,0.2); color: #a5b4fc; font-size: 0.65rem; border: 1px solid rgba(99,102,241,0.4);">
                                                    <i class="fa-solid fa-store me-1"></i>{{ $product->umkm->name }}
                                                </span>
                                            @endif

                                            @if ($product?->umkm && $variant?->branch)
                                                <span style="color: #475569;">|</span>
                                            @endif

                                            @if ($variant?->branch)
                                                <span class="badge px-2 py-1"
                                                    style="background: rgba(16,185,129,0.15); color: #6ee7b7; font-size: 0.65rem; border: 1px solid rgba(16,185,129,0.3);">
                                                    <i
                                                        class="fa-solid fa-location-dot me-1"></i>{{ $variant->branch->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="item-name">{{ $product?->name ?? '-' }}</div>

                                        {{-- Attributes --}}
                                        @if ($variant?->attributes)
                                            <div class="text-secondary small mb-1">
                                                {{ collect($variant->attributes)->map(fn($v, $k) => ucfirst($k) . ': ' . $v)->implode(' • ') }}
                                            </div>
                                        @endif

                                        <div class="item-price">
                                            Rp {{ number_format($price, 0, ',', '.') }} × {{ $qty }}
                                        </div>
                                    </div>

                                    <div class="item-subtotal">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </div>

                                </div>
                            @endforeach
                        @endif

                        <div class="checkout-total">
                            <span>Total</span>
                            <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>

                    </div>
                </div>

                {{-- ================= RIGHT ================= --}}
                <div class="col-lg-4">
                    <form action="{{ route('customer.checkout.store') }}" method="POST">
                        @csrf

                        @if (isset($isBuyNow) && $isBuyNow)
                            <input type="hidden" name="variant_id" value="{{ $buyNowItem->variant->id }}">
                            <input type="hidden" name="qty" value="{{ $buyNowItem->qty }}">
                        @endif

                        <div class="glass-card p-4">

                            {{-- PILIH CABANG --}}
                            <h5 class="section-title mb-3">Pilih Cabang</h5>

                            <input type="text" id="branchSearch" class="form-control checkout-textarea mb-3"
                                placeholder="Cari cabang (nama atau alamat)...">

                            <div id="branchContainer" class="branch-scroll">
                                @foreach ($branches as $branch)
                                    <label class="payment-option branch-item" data-name="{{ strtolower($branch->name) }}"
                                        data-address="{{ strtolower($branch->address) }}">

                                        <input type="radio" name="branch_id" value="{{ $branch->id }}" required>

                                        <div class="payment-content">
                                            <div class="payment-title">{{ $branch->name }}</div>
                                            <div class="payment-desc">
                                                {{ $branch->address ?? 'Alamat tidak tersedia' }}
                                            </div>
                                        </div>

                                    </label>
                                @endforeach
                            </div>

                            {{-- PILIH PEMBAYARAN --}}
                            <h5 class="section-title mt-4 mb-3">Metode Pembayaran</h5>

                            @foreach ($paymentMethods as $pm)
                                <label class="payment-option">
                                    <input type="radio" name="payment_method_id" value="{{ $pm->id }}" required>
                                    <div class="payment-content">
                                        <div class="payment-title">{{ $pm->name }}</div>
                                        <div class="payment-desc">
                                            {{ $pm->bank_name }} • {{ $pm->account_number }} • a.n
                                            {{ $pm->account_name }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach

                            {{-- CATATAN --}}
                            <div class="mt-4">
                                <label class="form-label text-secondary small">Catatan (opsional)</label>
                                <textarea name="note" rows="3" class="form-control checkout-textarea"
                                    placeholder="Contoh: warna sesuai foto ya...">{{ old('note') }}</textarea>
                            </div>

                            {{-- TOTAL --}}
                            <div class="checkout-summary mt-4">
                                <span>Total</span>
                                <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                            </div>

                            {{-- BUTTON --}}
                            <button type="submit" class="btn btn-modern w-100 mt-3">
                                <i class="fa-solid fa-lock"></i> Buat Pesanan
                            </button>

                            <div class="small text-muted mt-2">
                                * Stok akan divalidasi ulang saat proses checkout.
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .checkout-page {
            background: linear-gradient(135deg, #0b1220, #0f172a);
            min-height: 100vh;
        }

        .checkout-title {
            color: #fff;
            font-weight: 600;
        }

        /* ================= BUTTON BACK ================= */
        .btn-back {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .15);
            color: #fff;
            border-radius: 10px;
            padding: 6px 14px;
            transition: all .2s ease;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, .15);
            border-color: rgba(255, 255, 255, .25);
            color: #fff;
            /* 🔥 penting supaya text tidak hilang */
            transform: translateY(-1px);
        }


        /* ================= BUTTON MODERN ================= */
        .btn-modern {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            color: #fff !important;
            /* 🔥 cegah override bootstrap */
            transition: all .25s ease;
        }

        .btn-modern:hover {
            background: linear-gradient(135deg, #5b5eea, #7c3aed);
            color: #fff !important;
            /* 🔥 text tetap putih */
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, .45);
        }

        .btn-modern:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .btn-modern:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .3);
        }

        .glass-card {
            background: rgba(255, 255, 255, .05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 18px;
        }

        .section-title {
            color: #fff;
            font-weight: 600;
        }

        .checkout-item {
            display: flex;
            gap: 16px;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .item-img img {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 12px;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            color: #fff;
            font-weight: 600;
        }

        .item-variant,
        .item-price {
            font-size: 13px;
            color: #9ca3af;
        }

        .item-subtotal {
            color: #fff;
            font-weight: 600;
        }

        .checkout-total,
        .checkout-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            font-size: 18px;
            color: #fff;
        }

        .payment-option {
            display: flex;
            gap: 12px;
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 14px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all .2s ease;
        }

        .payment-option:hover {
            background: rgba(255, 255, 255, .05);
        }

        .payment-option input {
            margin-top: 6px;
        }

        .payment-option input:checked+.payment-content {
            color: #6366f1;
        }

        .payment-title {
            font-weight: 600;
            color: #fff;
        }

        .payment-desc {
            font-size: 13px;
            color: #9ca3af;
        }

        .checkout-textarea {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            color: #fff;
        }

        .btn-modern {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all .2s ease;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, .4);
        }

        /* Scroll area cabang */
        .branch-scroll {
            max-height: 250px;
            overflow-y: auto;
            padding-right: 6px;
        }

        /* Scrollbar styling */
        .branch-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .branch-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .2);
            border-radius: 10px;
        }

        /* Highlight saat dipilih */
        .payment-option input:checked+.payment-content {
            color: #6366f1;
        }

        .payment-option input:checked {
            accent-color: #6366f1;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function toggleBranchSearch() {
            let box = document.getElementById('branch-search-box');
            box.style.display = (box.style.display === 'none') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {

            const searchInput = document.getElementById('branchSearch');

            if (!searchInput) return;

            searchInput.addEventListener('keyup', function() {

                const keyword = this.value.toLowerCase().trim();

                const branches = document.querySelectorAll('.branch-item');

                branches.forEach(function(item) {

                    const name = item.dataset.name;
                    const address = item.dataset.address;

                    if (
                        name.includes(keyword) ||
                        address.includes(keyword)
                    ) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }

                });

            });

        });
    </script>
@endpush
