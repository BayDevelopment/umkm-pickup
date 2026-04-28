@extends('layouts.customer')

@section('customer')
    <div class="product-wrapper">

        <!-- HEADER -->
        <div class="product-header">
            <h2>Produk Kami 🛍️</h2>
            <p>Temukan produk terbaik untuk gaya dan kebutuhan kamu</p>
        </div>

        <!-- FILTER -->
        <form method="GET" class="filter-card mb-5">
            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label>Cari Produk</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control modern-input"
                        placeholder="Nama produk...">
                </div>

                <div class="col-md-2">
                    <label>Harga Min</label>
                    <input type="number" name="min_price" min="0" value="{{ request('min_price') }}"
                        class="form-control modern-input" placeholder="0">
                </div>

                <div class="col-md-2">
                    <label>Harga Max</label>
                    <input type="number" name="max_price" min="0" value="{{ request('max_price') }}"
                        class="form-control modern-input" placeholder="1000000">
                </div>

                <div class="col-md-2">
                    <label>Urutkan</label>
                    <select name="sort" class="form-select modern-input">
                        <option value="">Terbaru</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                            Harga Termurah
                        </option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                            Harga Termahal
                        </option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-modern w-100">
                        Filter
                    </button>
                    <a href="{{ route('customer.product') }}" class="btn btn-reset w-100">
                        Reset
                    </a>
                </div>

            </div>
        </form>

        <!-- PRODUCT GRID -->
        <div class="row g-4">
            @forelse ($products as $product)
                @php
                    $lowestPrice = $product->variants_min_price ?? ($product->variants->min('price') ?? 0);
                    $hasVariant = $product->variants->isNotEmpty();
                    $imgPath = $product->mainImage?->path
                        ? asset('storage/' . $product->mainImage->path)
                        : 'https://via.placeholder.com/400x300';
                    $branches = $product->variants
                        ->whereNotNull('branch_id')
                        ->map(fn($v) => $v->branch?->name)
                        ->filter()
                        ->unique()
                        ->values();
                @endphp

                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                    <div class="product-card h-100">

                        <!-- IMAGE -->
                        <div class="product-image">
                            <img src="{{ $imgPath }}" alt="{{ $product->name }}"
                                onerror="this.src='https://via.placeholder.com/400x300'; this.onerror=null;">
                        </div>

                        <!-- BODY -->
                        <div class="product-body">

                            {{-- UMKM & Branch --}}
                            <div class="d-flex align-items-center gap-1 mb-2" style="min-width:0;">

                                @if ($product->umkm)
                                    <span class="badge text-truncate px-2 py-1"
                                        style="background: rgba(99,102,241,0.2); color: #a5b4fc; font-size: 0.65rem; border: 1px solid rgba(99,102,241,0.4); max-width: 45%;">
                                        <i class="fa-solid fa-store me-1"></i>
                                        <span class="text-truncate">{{ $product->umkm->name }}</span>
                                    </span>
                                @endif

                                @if ($product->umkm && $branches->isNotEmpty())
                                    <span style="color: #475569; flex-shrink:0;">|</span>
                                @endif

                                @if ($branches->isNotEmpty())
                                    <span class="badge text-truncate px-2 py-1"
                                        style="background: rgba(16,185,129,0.15); color: #6ee7b7; font-size: 0.65rem; border: 1px solid rgba(16,185,129,0.3); max-width: 45%;">
                                        <i class="fa-solid fa-location-dot me-1"></i>
                                        <span class="text-truncate">{{ $branches->first() }}</span>
                                    </span>
                                @endif

                            </div>

                            <h6 class="text-truncate mb-1" title="{{ $product->name }}">
                                {{ $product->name }}
                            </h6>

                            <p class="text-truncate mb-3" title="{{ $product->description }}">
                                {{ \Illuminate\Support\Str::limit($product->description ?? '-', 60) }}
                            </p>

                            <div class="product-footer">
                                <div class="price">
                                    Rp {{ number_format($lowestPrice, 0, ',', '.') }}
                                </div>

                                <div class="action-buttons">
                                    @if ($hasVariant)
                                        <a href="{{ route('customer.detail.produk', [
                                            'category' => $product->category->slug,
                                            'product' => $product->slug,
                                        ]) }}"
                                            class="btn-action text-decoration-none">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @else
                                        <button class="btn-action opacity-50" disabled>
                                            <i class="fa-solid fa-eye-slash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="empty-state">
                        Produk belum tersedia 😢
                    </div>
                </div>
            @endforelse
        </div>

        <!-- PAGINATION -->
        @if ($products->hasPages())
            <div class="mt-5 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        @endif

    </div>
@endsection
@push('styles')
    <style>
        .product-wrapper {
            padding: 100px 30px 40px;
        }

        /* HEADER */
        .product-header h2 {
            font-weight: 700;
            color: #fff;
        }

        .product-header p {
            color: #9ca3af;
            margin-top: 5px;
        }

        /* FILTER CARD */
        .filter-card {
            background: rgba(255, 255, 255, 0.04);
            padding: 25px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .filter-card label {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .modern-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #fff;
            border-radius: 12px;
        }

        /* BUTTON */
        .btn-modern {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: white;
            border-radius: 12px;
        }

        .btn-modern:hover {
            opacity: 0.9;
        }

        .btn-reset {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 12px;
        }

        /* PRODUCT CARD */
        .product-card {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-6px);
            border-color: rgba(99, 102, 241, 0.4);
        }

        .product-image {
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .product-card:hover img {
            transform: scale(1.08);
        }

        .product-body {
            padding: 18px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-body h6 {
            color: #fff;
            font-weight: 600;
        }

        .product-body p {
            color: #9ca3af;
            font-size: 0.85rem;
            flex-grow: 1;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price {
            font-weight: 700;
            color: #fff;
        }

        .btn-cart {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 10px;
        }

        .btn-cart.disabled {
            background: #444;
            cursor: not-allowed;
        }

        .empty-state {
            padding: 60px;
            text-align: center;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 20px;
            color: #9ca3af;
        }
    </style>

    <script>
        document.querySelector('select[name="sort"]')
            ?.addEventListener('change', function() {
                this.form.submit();
            });
    </script>
@endpush
