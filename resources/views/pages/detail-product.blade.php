@extends('layouts.app')

@section('content')
    <section class="td-page td-page--after-navbar" style="background:#0b1220;">
        <div class="container">

            {{-- Breadcrumb --}}
            <nav class="td-breadcrumb mb-4">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="td-bc-link">Beranda</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url('/produk') }}" class="td-bc-link">Produk</a>
                    </li>
                    <li class="breadcrumb-item active td-bc-active">
                        {{ $product->name }}
                    </li>
                </ol>
            </nav>

            <div class="row g-4 align-items-start">

                {{-- Gallery --}}
                <div class="col-lg-6">
                    <div class="td-detail-gallery">

                        <div class="td-main-img">
                            <img id="mainProductImg"
                                src="{{ $product->mainImage?->path ? asset('storage/' . $product->mainImage->path) : asset('images/no-image.png') }}"
                                alt="{{ $product->name }}">

                            @if ($product->is_new)
                                <span class="td-badge bg-success">Baru</span>
                            @endif
                        </div>

                        {{-- Thumbnails (fix dari relasi images) --}}
                        <div class="td-thumbs mt-3">
                            @foreach ($product->images as $img)
                                <button type="button" class="td-thumb-btn"
                                    onclick="setMainImg('{{ asset('storage/' . $img->path) }}')">
                                    <img src="{{ asset('storage/' . $img->path) }}" alt="thumb">
                                </button>
                            @endforeach
                        </div>

                    </div>
                </div>

                {{-- Info --}}
                <div class="col-lg-6">
                    <div class="td-detail-card">

                        {{-- UMKM & Cabang --}}
                        <div class="d-flex align-items-center gap-2 mb-3">
                            @if ($product->umkm)
                                <span class="badge px-2 py-1"
                                    style="background: rgba(99,102,241,0.2); color: #a5b4fc; font-size: 0.75rem; border: 1px solid rgba(99,102,241,0.4);">
                                    <i class="fa-solid fa-store me-1"></i>{{ $product->umkm->name }}
                                </span>
                            @endif

                            @if ($product->umkm && $product->variants->first()?->branch)
                                <span style="color: #475569;">|</span>
                            @endif

                            @if ($product->variants->first()?->branch)
                                <span class="badge px-2 py-1"
                                    style="background: rgba(16,185,129,0.15); color: #6ee7b7; font-size: 0.75rem; border: 1px solid rgba(16,185,129,0.3);">
                                    <i class="fa-solid fa-location-dot me-1"></i>
                                    {{ $product->variants->first()->branch->name }}
                                </span>
                            @endif
                        </div>

                        <h1 class="td-detail-title">{{ $product->name }}</h1>

                        <div class="mb-3">
                            <span id="stockInfoText" class="td-stock text-warning">
                                Pilih variant terlebih dahulu
                            </span>
                        </div>

                        {{-- Harga --}}
                        <div class="td-detail-price mb-4">
                            <div class="td-price-now" id="productPrice">
                                Rp {{ number_format($product->lowest_price ?? 0, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- Variants --}}
                        @php
                            $grouped = [];
                            foreach ($product->variants as $variant) {
                                foreach ($variant->attributes ?? [] as $key => $value) {
                                    $grouped[$key][] = $value;
                                }
                            }
                            foreach ($grouped as $key => $values) {
                                $grouped[$key] = array_unique($values);
                            }

                            $PILL_MAX = 4;
                        @endphp

                        @if ($product->variants->count())
                            <div class="mb-4">
                                <div class="td-variant-label mb-3">Pilih Variant</div>

                                @foreach ($grouped as $attr => $values)
                                    <div class="mb-3">
                                        <div class="td-variant-label mb-2">{{ ucfirst($attr) }}</div>

                                        @if (count($values) <= $PILL_MAX)
                                            <div class="td-variant-options">
                                                @foreach ($values as $val)
                                                    <button type="button" class="td-pill-attr"
                                                        data-attr="{{ $attr }}" data-value="{{ $val }}">
                                                        {{ $val }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @else
                                            <select class="td-select-attr form-select" data-attr="{{ $attr }}"
                                                style="background:#1a2235;color:#fff;border-color:rgba(255,255,255,.2);max-width:260px">
                                                <option value="">-- Pilih {{ ucfirst($attr) }} --</option>
                                                @foreach ($values as $val)
                                                    <option value="{{ $val }}">{{ $val }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Quantity --}}
                        <div class="mb-4">
                            <div class="td-variant-label mb-2">
                                Jumlah
                                <span id="stockInfo" style="font-size:12px;color:#aaa;"></span>
                            </div>

                            <div class="td-qty-control">
                                <button type="button" class="td-qty-btn" onclick="qtyMinus()">−</button>
                                <input id="qtyInput" class="td-qty-input" type="number" value="1" min="1"
                                    disabled>
                                <button type="button" class="td-qty-btn" onclick="qtyPlus()">+</button>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <form id="productActionForm" method="POST" class="w-100">
                            @csrf
                            <input type="hidden" name="variant_id" id="formVariantId">
                            <input type="hidden" name="qty" id="formQty">

                            <div class="d-flex gap-2">
                                <button type="button" onclick="submitCart()" class="btn btn-outline-td w-100">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    Tambah ke Keranjang
                                </button>

                                <button type="button" onclick="submitBuyNow()" class="btn btn-td w-100">
                                    <i class="fa-solid fa-bolt"></i>
                                    Beli Sekarang
                                </button>
                            </div>
                        </form>

                        <hr style="border-color: rgba(255,255,255,.12)" class="my-4">

                        {{-- Description --}}
                        <p class="td-subtitle text-white mb-0">
                            {{ $product->description }}
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        #mainProductImg {
            transition: opacity .2s ease;
        }

        /* =========================
                                                                   VARIANT ATTRIBUTE (MATCH SYSTEM)
                                                                   ========================= */

        .td-pill-attr {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;

            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.02);
            color: rgba(255, 255, 255, 0.85);

            cursor: pointer;
            transition: all 0.2s ease;
        }

        /* hover sama seperti td-pill */
        .td-pill-attr:hover {
            border-color: rgba(111, 66, 193, 0.5);
        }

        /* active sama seperti checked */
        .td-pill-attr.active {
            background: rgba(111, 66, 193, 0.22);
            border-color: rgba(111, 66, 193, 0.6);
            color: #fff;
        }

        /* Pill disabled */
        .td-pill-disabled {
            opacity: 0.35;
            cursor: not-allowed;
            text-decoration: line-through;
            pointer-events: none;
        }

        /* Dropdown style */
        .td-select-attr {
            background: #1a2235;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 8px;
            padding: 8px 12px;
            max-width: 260px;
            width: 100%;
        }

        .td-select-attr:focus {
            outline: none;
            border-color: rgba(255, 255, 255, .5);
        }

        .td-select-attr option {
            background: #1a2235;
            color: #fff;
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.setMainImg = function(src) {
            const mainImg = document.getElementById('mainProductImg');
            if (!mainImg || !src) return;
            mainImg.style.opacity = 0;
            setTimeout(() => {
                mainImg.src = src + '?t=' + Date.now();
                mainImg.style.opacity = 1;
            }, 100);
        }

        document.addEventListener('DOMContentLoaded', function() {

            const isLoggedIn = @json(auth()->check());
            const variants = @json($product->variants);

            const qtyInput = document.getElementById('qtyInput');
            const stockInfo = document.getElementById('stockInfo');
            const stockInfoText = document.getElementById('stockInfoText');
            const mainImg = document.getElementById('mainProductImg');
            const form = document.getElementById('productActionForm');
            const buttons = form ? form.querySelectorAll('button') : [];

            let selectedAttrs = {};
            let activeVariant = null;

            // INIT — semua disabled sampai user pilih sendiri
            if (qtyInput) qtyInput.disabled = true;
            buttons.forEach(btn => btn.disabled = true);

            // hitung total group attr dari DOM
            const totalGroups = new Set(
                [...document.querySelectorAll('.td-pill-attr, .td-select-attr')]
                .map(el => el.dataset.attr).filter(Boolean)
            ).size;

            // ===============================
            // HELPERS
            // ===============================
            function updatePrice(price) {
                const els = [
                    document.getElementById('productPrice'),
                    document.querySelector('.td-price-now')
                ];
                els.forEach(el => {
                    if (el) el.innerText = 'Rp ' + Number(price).toLocaleString('id-ID');
                });
            }

            function updateImage(src) {
                if (!mainImg || !src) return;
                mainImg.style.opacity = 0;
                setTimeout(() => {
                    mainImg.src = src + '?t=' + Date.now();
                    mainImg.style.opacity = 1;
                }, 100);
            }

            function updateStock(stock) {
                const hasStock = stock > 0;
                if (stockInfo) {
                    stockInfo.innerHTML = hasStock ?
                        'Stok tersedia: ' + stock :
                        '<span style="color:red">Stok habis</span>';
                }
                if (stockInfoText) {
                    stockInfoText.innerHTML = hasStock ? 'Stok tersedia' : 'Pilih variant terlebih dahulu';
                }
                const tdStock = document.querySelector('.td-stock');
                if (tdStock) tdStock.innerText = hasStock ? 'Stok tersedia: ' + stock : 'Stok habis';
            }

            function updateQty(stock) {
                if (!qtyInput) return;
                qtyInput.value = 1;
                qtyInput.max = stock;
                qtyInput.disabled = stock <= 0;
            }

            function toggleButtons(stock) {
                buttons.forEach(btn => btn.disabled = stock <= 0);
            }

            // ===============================
            // CORE
            // ===============================
            function applyVariant(variant) {
                if (!variant) {
                    updatePrice(0);
                    updateStock(0);
                    updateQty(0);
                    toggleButtons(0);
                    return;
                }
                activeVariant = variant;
                updatePrice(variant.price ?? 0);
                updateStock(variant.stock ?? 0);
                updateQty(variant.stock ?? 0);
                toggleButtons(variant.stock ?? 0);
                if (variant.image) updateImage(variant.image);
            }

            // ===============================
            // FIND VARIANT
            // ===============================
            function findVariantByAttrs() {
                // belum semua group dipilih — tunggu dulu
                if (Object.keys(selectedAttrs).length < totalGroups) {
                    applyVariant(null);
                    return;
                }

                // exact match
                const match = variants.find(v => {
                    const attrs = v.attributes || {};
                    return Object.keys(selectedAttrs).every(key => attrs[key] == selectedAttrs[key]);
                });

                applyVariant(match ?? null);
            }

            // ===============================
            // PILLS listener
            // ===============================
            document.querySelectorAll('.td-pill-attr').forEach(btn => {
                btn.addEventListener('click', function() {
                    const attr = this.dataset.attr;
                    const value = this.dataset.value;

                    document.querySelectorAll(`.td-pill-attr[data-attr="${attr}"]`)
                        .forEach(el => el.classList.remove('active'));
                    this.classList.add('active');

                    selectedAttrs[attr] = value;
                    findVariantByAttrs();
                });
            });

            // ===============================
            // DROPDOWN listener
            // ===============================
            document.querySelectorAll('.td-select-attr').forEach(select => {
                select.addEventListener('change', function() {
                    const attr = this.dataset.attr;
                    const value = this.value;

                    if (!value) {
                        delete selectedAttrs[attr];
                    } else {
                        selectedAttrs[attr] = value;
                    }

                    findVariantByAttrs();
                });
            });

            // ===============================
            // QTY
            // ===============================
            window.qtyMinus = function() {
                if (!qtyInput || qtyInput.disabled) return;
                qtyInput.value = Math.max(1, parseInt(qtyInput.value || 1) - 1);
            }

            window.qtyPlus = function() {
                if (!qtyInput || qtyInput.disabled) return;
                qtyInput.value = Math.min(parseInt(qtyInput.max || 1), parseInt(qtyInput.value || 1) + 1);
            }

            // ===============================
            // CART
            // ===============================
            window.submitCart = function() {
                if (!activeVariant) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih variant dulu'
                    });
                    return;
                }
                document.getElementById('formVariantId').value = activeVariant.id;
                document.getElementById('formQty').value = qtyInput.value;
                form.action = "{{ route('cart.add') }}";
                form.submit();
            }

            // ===============================
            // BUY NOW
            // ===============================
            window.submitBuyNow = function() {
                if (!activeVariant) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Variant belum dipilih',
                        text: 'Silakan pilih variant terlebih dahulu.'
                    });
                    return;
                }
                if (!qtyInput || qtyInput.disabled) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok tidak tersedia'
                    });
                    return;
                }
                if (isLoggedIn) {
                    document.getElementById('formVariantId').value = activeVariant.id;
                    document.getElementById('formQty').value = qtyInput.value;
                    form.action = "{{ route('customer.buy.now') }}";
                    form.submit();
                    return;
                }
                Swal.fire({
                    icon: 'question',
                    title: 'Upss',
                    text: 'Login untuk melakukan pembelian.',
                    confirmButtonText: 'Login',
                    cancelButtonText: 'Nanti saja',
                    showCancelButton: true
                }).then(result => {
                    if (result.isConfirmed) window.location.href = "{{ route('login') }}";
                });
            }

        });
    </script>
@endpush
