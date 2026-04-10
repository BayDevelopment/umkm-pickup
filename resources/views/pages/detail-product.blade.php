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
                        @if ($product->variants->count())
                            <div class="mb-4">
                                <div class="td-variant-label mb-2">Pilih Variant</div>

                                <div class="td-variant-options">
                                    @foreach ($product->variants as $variant)
                                        <label class="td-pill">
                                            <input type="radio" name="variant_id" value="{{ $variant->id }}"
                                                data-stock="{{ $variant->stock }}" data-price="{{ $variant->price }}"
                                                data-color="{{ $variant->color }}" data-size="{{ $variant->size }}"
                                                data-image="{{ $variant->image ? asset('storage/' . $variant->image) : asset('storage/' . $product->image[0]) }}"
                                                {{ $variant->stock == 0 ? 'disabled' : '' }}>

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
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const isLoggedIn = @json(auth()->check());

            const radios = document.querySelectorAll('input[name="variant_id"]');
            const priceEl = document.getElementById('productPrice');
            const qtyInput = document.getElementById('qtyInput');
            const stockInfo = document.getElementById('stockInfo');
            const stockInfoText = document.getElementById('stockInfoText');
            const mainImg = document.getElementById('mainProductImg');
            const form = document.getElementById('productActionForm');
            const buttons = form ? form.querySelectorAll('button') : [];

            // ===============================
            // INIT
            // ===============================
            if (qtyInput) qtyInput.disabled = true;
            buttons.forEach(btn => btn.disabled = true);

            // ===============================
            // HELPERS
            // ===============================

            function updatePrice(price) {
                if (!priceEl) return;
                priceEl.textContent = 'Rp ' + Number(price).toLocaleString('id-ID');
            }

            function updateImage(src) {
                if (!mainImg || !src) return;

                mainImg.style.opacity = 0;
                setTimeout(() => {
                    mainImg.src = src + '?t=' + Date.now(); // anti cache
                    mainImg.style.opacity = 1;
                }, 100);
            }

            function updateStock(stock) {
                if (stockInfo) {
                    stockInfo.innerHTML = stock > 0 ?
                        'Stok tersedia: ' + stock :
                        '<span style="color:red">Stok habis</span>';
                }

                if (stockInfoText) {
                    stockInfoText.innerHTML = stock > 0 ?
                        'Stok tersedia' :
                        'Stok habis';
                }
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

            function setActiveVariant(el) {
                document.querySelectorAll('.td-pill').forEach(p => {
                    p.classList.remove('active');
                });

                const parent = el.closest('.td-pill');
                if (parent) parent.classList.add('active');
            }

            function getSelectedVariant() {
                return document.querySelector('input[name="variant_id"]:checked');
            }

            // ===============================
            // CORE (SINGLE SOURCE OF TRUTH)
            // ===============================
            function applyVariant(radio) {
                const stock = parseInt(radio.dataset.stock || 0);
                const price = parseFloat(radio.dataset.price || 0);
                const image = radio.dataset.image;

                updatePrice(price);
                updateStock(stock);
                updateQty(stock);
                updateImage(image);
                toggleButtons(stock);
                setActiveVariant(radio);
            }

            // ===============================
            // EVENT VARIANT
            // ===============================
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    applyVariant(this);
                });
            });

            // ===============================
            // AUTO SELECT
            // ===============================
            const first = document.querySelector('input[name="variant_id"]:not(:disabled)');
            if (first) {
                first.checked = true;
                applyVariant(first);
            }

            // ===============================
            // QTY
            // ===============================
            window.qtyMinus = function() {
                if (!qtyInput || qtyInput.disabled) return;
                qtyInput.value = Math.max(1, parseInt(qtyInput.value || 1) - 1);
            }

            window.qtyPlus = function() {
                if (!qtyInput || qtyInput.disabled) return;
                const max = parseInt(qtyInput.max || 1);
                qtyInput.value = Math.min(max, parseInt(qtyInput.value || 1) + 1);
            }

            // ===============================
            // CART
            // ===============================
            window.submitCart = function() {
                const selected = getSelectedVariant();

                if (!selected) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih variant dulu'
                    });
                    return;
                }

                document.getElementById('formVariantId').value = selected.value;
                document.getElementById('formQty').value = qtyInput.value;

                form.action = "{{ route('cart.add') }}";
                form.submit();
            }

            // ===============================
            // 🔥 BUY NOW (FINAL FIX)
            // ===============================
            window.submitBuyNow = function() {

                const selected = getSelectedVariant();

                if (!selected) {
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

                Swal.fire({
                    icon: 'question',
                    title: 'Upss',
                    text: 'Login untuk melakukan pembelian.',
                    confirmButtonText: 'Login',
                    cancelButtonText: 'Nanti saja',
                    showCancelButton: true
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    // 🔥 CHECK LOGIN (FINAL)
                    if (!isLoggedIn) {
                        window.location.href = "{{ route('login') }}";
                        return;
                    }

                    // 🔥 SUBMIT
                    document.getElementById('formVariantId').value = selected.value;
                    document.getElementById('formQty').value = qtyInput.value;

                    form.action = "{{ route('customer.buy.now') }}";
                    form.submit();
                });
            }

        });

        window.setMainImg = function(src) {
            const mainImg = document.getElementById('mainProductImg');

            if (!mainImg || !src) return;

            mainImg.style.opacity = 0;

            setTimeout(() => {
                mainImg.src = src + '?t=' + Date.now(); // anti cache
                mainImg.style.opacity = 1;
            }, 100);
        }
    </script>
@endpush
