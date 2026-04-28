@extends('layouts.app')

@section('content')
    <!-- Tambah padding atas ekstra biar tidak tertutup navbar (sesuaikan 80px atau lebih kalau navbar fixed + shadow) -->
    <div class="container-fluid py-5 px-md-5 pt-5 pt-md-6"
        style="background: linear-gradient(135deg, #0b1220, #0f172a); min-height: 100vh; padding-top: 100px !important;">

        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold text-white mb-1">Produk Kami 🛍️</h2>
                <p class="text-secondary mb-0">Temukan produk terbaik untuk kebutuhanmu hari ini</p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-primary px-3 py-2 fs-6">
                    {{ $products->total() }} Produk Tersedia
                </span>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="td-card glass-card p-4 mb-5 shadow-lg">
            <form method="GET" class="row g-3 align-items-end">

                {{-- SEARCH --}}
                <div class="col-md-4 position-relative">
                    <input type="text" name="search" value="{{ request('search') }}" maxlength="100"
                        class="form-control td-input ps-5" placeholder="Cari produk...">
                    <i class="fa-solid fa-search position-absolute top-50 start-3 translate-middle-y text-muted"></i>
                </div>

                {{-- MIN PRICE --}}
                <div class="col-md-2">
                    <input type="number" name="min_price" min="0" step="1000" value="{{ request('min_price') }}"
                        class="form-control td-input" placeholder="Harga Min">
                </div>

                {{-- MAX PRICE --}}
                <div class="col-md-2">
                    <input type="number" name="max_price" min="0" step="1000" value="{{ request('max_price') }}"
                        class="form-control td-input" placeholder="Harga Max">
                </div>

                {{-- SORT --}}
                <div class="col-md-2">
                    <select name="sort" class="form-select td-input">
                        <option value="">Terbaru</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                            Harga Termurah
                        </option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                            Harga Termahal
                        </option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>
                            A-Z
                        </option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                            Z-A
                        </option>
                    </select>
                </div>

                {{-- BUTTON --}}
                <div class="col-md-2 d-flex gap-2 justify-content-end">

                    <button type="submit" class="btn btn-td btn-sm px-3">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>

                    <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm px-3">
                        <i class="fa-solid fa-rotate-left me-1"></i>
                    </a>

                </div>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="row g-4 product-grid">
            @forelse ($products as $product)
                @php
                    $lowestPrice = $product->lowest_price ?? 0;
                    $hasStock = $product->variants->sum('stock') > 0;
                    $description = Str::limit($product->description ?? 'Tidak ada deskripsi', 80, '...');
                    $imgPath = $product->mainImage ? asset('storage/' . $product->mainImage->path) : null;

                    // Ambil branch unik dari semua variant
                    $branches = $product->variants
                        ->whereNotNull('branch_id')
                        ->map(fn($v) => $v->branch?->name)
                        ->filter()
                        ->unique()
                        ->values();
                @endphp

                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="td-product-card h-100 shadow-lg overflow-hidden position-relative d-flex flex-column">

                        <!-- Image -->
                        <div class="product-img-wrapper position-relative" style="height: 280px;">
                            @if ($imgPath)
                                <img loading="lazy" src="{{ $imgPath }}" alt="{{ $product->name }}"
                                    class="td-product-img w-100 h-100 object-fit-cover">
                            @else
                                <img loading="lazy"
                                    src="https://via.placeholder.com/480x480/1e1b4b/ffffff?text={{ Str::slug($product->name) }}"
                                    alt="{{ $product->name }}" class="td-product-img w-100 h-100 object-fit-cover">
                            @endif

                            <!-- Badge -->
                            <div class="position-absolute top-0 start-0 p-3 d-flex flex-column gap-2">
                                @if (!$hasStock)
                                    <span class="badge bg-danger px-3 py-2">Stok Habis</span>
                                @endif
                                @if ($product->is_new)
                                    <span class="badge bg-success px-3 py-2">New Arrival</span>
                                @endif
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="td-product-body p-4 d-flex flex-column flex-grow-1">

                            {{-- UMKM Badge --}}
                            @if ($product->umkm)
                                <div class="mb-2">
                                    <span class="badge px-2 py-1"
                                        style="background: rgba(99,102,241,0.2); color: #a5b4fc; font-size: 0.7rem; border: 1px solid rgba(99,102,241,0.4);">
                                        <i class="fa-solid fa-store me-1"></i>{{ $product->umkm->name }}
                                    </span>
                                </div>
                            @endif

                            {{-- Nama Produk --}}
                            <h6 class="fw-bold text-white mb-2 text-truncate">
                                {{ $product->name }}
                            </h6>

                            {{-- Deskripsi --}}
                            <p class="text-secondary small mb-3 flex-grow-1 text-truncate-3" style="line-height: 1.5;">
                                {{ $description }}
                            </p>

                            {{-- Branch --}}
                            @if ($branches->isNotEmpty())
                                <div class="mb-3 d-flex flex-wrap gap-1">
                                    @foreach ($branches as $branch)
                                        <span class="badge px-2 py-1"
                                            style="background: rgba(16,185,129,0.15); color: #6ee7b7; font-size: 0.65rem; border: 1px solid rgba(16,185,129,0.3);">
                                            <i class="fa-solid fa-location-dot me-1"></i>{{ $branch }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Harga & Tombol --}}
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Mulai dari</div>
                                    <div class="fw-bold fs-5 text-white">
                                        Rp {{ number_format($lowestPrice, 0, ',', '.') }}
                                    </div>
                                </div>

                                <a href="{{ route('products.detail', ['category' => $product->category->slug ?? 'all', 'product' => $product->slug]) }}"
                                    class="btn btn-sm btn-td rounded-pill px-4">
                                    <i class="fa-solid fa-eye me-2"></i>Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="empty-state text-center py-5">
                        <i class="fa-solid fa-box-open fa-5x text-muted mb-4"></i>
                        <h4 class="text-white mb-3">Tidak ada produk ditemukan</h4>
                        <p class="text-secondary">Coba ubah kata kunci atau filter pencarianmu</p>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-light mt-3 px-4">
                            Reset Filter
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($products->hasPages())
            <div class="mt-5 d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>
@endsection

@push('styles')
    <style>
        .td-product-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .td-product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 60px rgba(99, 102, 241, 0.25);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .product-img-wrapper {
            height: 280px;
            overflow: hidden;
        }

        .td-product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .td-product-card:hover .td-product-img {
            transform: scale(1.08);
        }

        .td-product-body {
            padding: 1.5rem;
        }

        .text-truncate-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
            max-height: 4.5em;
            /* 3 baris x line-height */
        }

        .btn-td {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            transition: all 0.3s;
        }

        .btn-td:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.4);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
        }

        .empty-state i {
            opacity: 0.6;
        }

        .form-control.td-input,
        .form-select.td-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .form-control.td-input:focus,
        .form-select.td-input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #6366f1;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
        }

        /* Padding atas ekstra untuk navbar fixed */
        .td-page--after-navbar {
            padding-top: 100px !important;
            /* sesuaikan dengan tinggi navbar + shadow */
        }

        @media (max-width: 992px) {
            .td-page--after-navbar {
                padding-top: 120px !important;
            }
        }
    </style>
@endpush
