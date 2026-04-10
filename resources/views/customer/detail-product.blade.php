@extends('layouts.customer')

@section('customer')
    <section class="td-page td-page--after-navbar" style="background:#0b1220;">
        <div class="container">

            {{-- Breadcrumb --}}
            <nav class="td-breadcrumb mb-4">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="td-bc-link">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url('/customer/product') }}" class="td-bc-link">Produk</a>
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
                                src="{{ $product->image && count($product->image)
                                    ? asset('storage/' . $product->image[0])
                                    : asset('images/no-image.png') }}"
                                alt="{{ $product->name }}">

                            @if ($product->created_at->diffInDays(now()) < 4)
                                <span class="td-badge bg-success">Baru</span>
                            @endif
                        </div>

                        <div class="td-thumbs mt-3">
                            @foreach ($product->image ?? [] as $img)
                                <button type="button" class="td-thumb-btn"
                                    onclick="setMainImg('{{ asset('storage/' . $img) }}')">
                                    <img src="{{ asset('storage/' . $img) }}" alt="thumb">
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="col-lg-6">
                    <div class="td-detail-card">

                        <div id="productDetailContent" class="d-none">

                            <h1 class="td-detail-title">{{ $product->name }}</h1>

                            <div class="mb-3">
                                <span class="td-stock {{ $product->is_in_stock ? '' : 'is-out' }}">
                                    {{ $product->is_in_stock ? 'Stok tersedia: ' . $product->total_stock : 'Stok habis' }}
                                </span>
                            </div>

                            {{-- Harga --}}
                            <div class="td-detail-price mb-4">
                                <div class="td-price-now" id="productPrice">
                                    Rp {{ number_format($product->lowest_price ?? 0, 0, ',', '.') }}
                                </div>
                            </div>

                        </div>

                        {{-- Branch Availability --}}
                        @if ($product->variants->count())
                            <div class="mb-4">
                                <div class="td-variant-label mb-2">Tersedia di Cabang</div>

                                <div class="td-branch-list">
                                    <div id="selectedBranch" class="td-branch-item">
                                        📍 Pilih variant
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Variants --}}
                        @if ($product->variants->count())
                            <div class="mb-4">
                                <div class="td-variant-label mb-2">Pilih Variant</div>

                                <div class="td-variant-options">
                                    @foreach ($product->variants as $variant)
                                        <label class="td-pill">
                                            <input type="radio" name="variant_id" value="{{ $variant->id }}"
                                                data-stock="{{ $variant->stock }}" data-price="{{ $variant->price }}"
                                                data-branch="{{ $variant->branch->name ?? '-' }}">

                                            <span>
                                                {{ $variant->color }}
                                                {{ $variant->size ? '- ' . $variant->size : '' }}
                                                @if ($variant->stock == 0)
                                                    - <span style="color:red;">Stok Habis</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
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

        .td-branch-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .td-branch-item {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .08);
            padding: 6px 12px;
            border-radius: 8px;

            color: #fff !important;
            font-size: 13px;
            font-weight: 500;

            display: flex;
            align-items: center;
            gap: 6px;
        }

        .td-branch-item * {
            color: #fff !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const isLoggedIn = @json(auth()->check());

            const radios = document.querySelectorAll('input[name="variant_id"]');
            const qtyInput = document.getElementById('qtyInput');
            const stockInfo = document.getElementById('stockInfo');
            const form = document.getElementById('productActionForm');
            const buttons = form ? form.querySelectorAll('button') : [];

            const detail = document.getElementById("productDetailContent");
            const priceEl = document.getElementById("productPrice");

            // 🔥 NEW
            const branchDisplay = document.getElementById('selectedBranch');

            // =========================
            // INIT
            // =========================
            if (qtyInput) qtyInput.disabled = true;
            buttons.forEach(btn => btn.disabled = true);

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function showDetail() {
                if (detail && detail.classList.contains('d-none')) {
                    detail.classList.remove('d-none');
                }
            }

            // =========================
            // VARIANT CHANGE (UPDATED)
            // =========================
            radios.forEach(radio => {
                radio.addEventListener('change', function() {

                    const stock = parseInt(this.dataset.stock || 0);
                    const price = parseInt(this.dataset.price || 0);

                    // 🔥 FIX BRANCH (SIMPLE & CLEAN)
                    const branchDisplay = document.getElementById('selectedBranch');
                    const branchName = this.dataset.branch || '-';

                    if (branchDisplay) {
                        branchDisplay.innerHTML = "📍 " + branchName;
                    }

                    // existing logic tetap
                    showDetail();

                    if (qtyInput) {
                        qtyInput.value = 1;
                        qtyInput.max = stock;
                        qtyInput.disabled = stock <= 0;
                    }

                    if (stockInfo) {
                        stockInfo.innerHTML = stock > 0 ?
                            'Stok tersedia: ' + stock :
                            'Stok habis';
                    }

                    if (priceEl) {
                        priceEl.innerText = "Rp " + formatRupiah(price);
                    }

                    buttons.forEach(btn => btn.disabled = stock <= 0);
                });
            });

            // =========================
            // GLOBAL FUNCTIONS
            // =========================

            window.setMainImg = function(src) {
                const mainImg = document.getElementById('mainProductImg');
                if (!mainImg) return;

                mainImg.style.opacity = 0;

                setTimeout(() => {
                    mainImg.src = src;
                    mainImg.style.opacity = 1;
                }, 150);

                showDetail();
            }

            window.qtyMinus = function() {
                if (!qtyInput || qtyInput.disabled) return;
                qtyInput.value = Math.max(1, parseInt(qtyInput.value || 1) - 1);
            }

            window.qtyPlus = function() {
                if (!qtyInput || qtyInput.disabled) return;
                const max = parseInt(qtyInput.max || 1);
                qtyInput.value = Math.min(max, parseInt(qtyInput.value || 1) + 1);
            }

            window.getSelectedVariant = function() {
                return document.querySelector('input[name="variant_id"]:checked');
            }

            window.validateProductSelection = function() {

                const selected = getSelectedVariant();

                if (!selected) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Variant belum dipilih',
                        text: 'Silakan pilih variant terlebih dahulu.',
                        confirmButtonColor: '#6366f1'
                    });
                    return false;
                }

                if (!qtyInput || qtyInput.disabled) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jumlah tidak valid',
                        text: 'Silakan pilih variant dengan stok tersedia.',
                        confirmButtonColor: '#6366f1'
                    });
                    return false;
                }

                return true;
            }

            window.submitCart = function() {

                if (!validateProductSelection()) return;

                const selected = getSelectedVariant();

                document.getElementById('formVariantId').value = selected.value;
                document.getElementById('formQty').value = qtyInput.value;

                form.action = "{{ route('customer.cart.add') }}";
                form.submit();
            }

            window.submitBuyNow = function() {

                if (!validateProductSelection()) return;

                const selected = getSelectedVariant();

                document.getElementById('formVariantId').value = selected.value;
                document.getElementById('formQty').value = qtyInput.value;

                form.action = "{{ route('customer.buy.now') }}";
                form.submit();
            };

        });
    </script>
@endpush
