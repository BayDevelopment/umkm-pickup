@extends('layouts.app')

@section('content')
    <!-- HERO -->
    <section id="home" class="td-hero min-vh-100 d-flex align-items-center td-top-space">
        <div class="container py-5">
            <div class="row align-items-center g-5">

                <!-- LEFT -->
                <div class="col-lg-6">

                    <div class="td-badge mb-3">
                        <span>⚡</span>
                        <span>Modern • Cepat • Responsif</span>
                    </div>

                    <h1 class="display-5 fw-bold lh-1">
                        Belanja UMKM Lokal,
                        <span
                            style="background:linear-gradient(135deg,#a78bfa,#60a5fa);-webkit-background-clip:text;background-clip:text;color:transparent;">
                            Ambil di Tempat
                        </span>
                    </h1>

                    <p class="lead td-subtitle mt-3">
                        TrendoraPick adalah platform katalog fashion & lifestyle berbasis UMKM pickup yang rapi, elegan, dan
                        siap berkembang — dari pelaku usaha kecil hingga brand besar.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                        <a href="#products" class="btn btn-td btn-lg td-btn-hero">
                            <i class="fa-solid fa-store"></i>
                            <span>Lihat Katalog</span>
                        </a>
                    </div>

                    <div class="td-chip-slider mt-4">
                        <div class="td-chip-track">
                            <span class="td-chip">SEO-friendly</span>
                            <span class="td-chip">Mobile-first</span>
                            <span class="td-chip">Admin-ready</span>
                            <span class="td-chip">Fast UI</span>

                            <!-- duplicate untuk loop seamless -->
                            <span class="td-chip">SEO-friendly</span>
                            <span class="td-chip">Mobile-first</span>
                            <span class="td-chip">Admin-ready</span>
                            <span class="td-chip">Fast UI</span>
                        </div>
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="col-lg-6">

                    <div class="td-card p-4">

                        <div class="row g-3">

                            <!-- TOTAL PRODUK -->
                            <div class="col-6">
                                <div class="td-card p-3" style="border-radius:16px;">
                                    <div class="small td-subtitle">Produk</div>

                                    <div class="h3 fw-bold mb-0">
                                        {{ number_format($totalProducts, 0, ',', '.') }}
                                    </div>

                                </div>
                            </div>


                            <!-- TOTAL KATEGORI -->
                            <div class="col-6">
                                <div class="td-card p-3" style="border-radius:16px;">
                                    <div class="small td-subtitle">Kategori</div>

                                    <div class="h3 fw-bold mb-0">
                                        {{ number_format($totalCategories, 0, ',', '.') }}
                                    </div>

                                </div>
                            </div>


                            <!-- REKOMENDASI -->
                            <div class="col-12">

                                <div class="td-card p-3" style="border-radius:16px;">

                                    <div class="d-flex justify-content-between align-items-center">

                                        <div>
                                            <div class="fw-semibold">Rekomendasi Hari Ini</div>

                                            <div class="small td-subtitle">
                                                {{ $products->count() }} produk terbaru tersedia
                                            </div>
                                        </div>

                                        <span class="td-chip">Live</span>

                                    </div>

                                    <div class="mt-3">

                                        @php
                                            $percent =
                                                $totalProducts > 0
                                                    ? min(100, ($products->count() / $totalProducts) * 100)
                                                    : 0;
                                        @endphp

                                        <div class="progress" style="height:10px;background:rgba(255,255,255,.08);">

                                            <div class="progress-bar" style="width: {{ $percent }}%;"></div>

                                        </div>

                                        <div class="small td-subtitle mt-2">
                                            {{ number_format($percent, 0) }}% dari total produk
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>


    <!-- FEATURES -->
    <section id="features" class="td-section py-5">

        <div class="container py-2">

            <div class="text-center mb-5">
                <h2 class="fw-bold">Kenapa Trendora?</h2>
                <p class="td-subtitle mb-0">
                    Modern, cepat, dan mudah dikelola.
                </p>
            </div>

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="td-card p-4">
                        <div class="td-icon mb-3">🛍️</div>
                        <h5 class="fw-semibold">Katalog Lengkap</h5>
                        <p class="td-subtitle mb-0">
                            Tampilkan produk dengan kategori jelas, foto tajam,
                            dan detail yang rapi.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="td-card p-4">
                        <div class="td-icon mb-3">⚡</div>
                        <h5 class="fw-semibold">Cepat & Responsif</h5>
                        <p class="td-subtitle mb-0">
                            Optimasi pengalaman pengguna dengan tampilan
                            mobile-first & performa ringan.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="td-card p-4">
                        <div class="td-icon mb-3">🔒</div>
                        <h5 class="fw-semibold">Aman & Terpercaya</h5>
                        <p class="td-subtitle mb-0">
                            Struktur siap untuk autentikasi admin dan
                            pengelolaan data yang aman.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- PRODUCTS -->
    <section id="products" class="td-section py-5"
        style="background: linear-gradient(180deg,#0b1220 0%,#0b1220 60%,rgba(255,255,255,.02) 100%);">

        <div class="container py-2">

            <!-- HEADER -->
            <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">

                <div>
                    <h2 class="fw-bold mb-1">Produk Unggulan</h2>
                    <p class="td-subtitle mb-0">
                        Pilihan terbaik dari Trendora.
                    </p>
                </div>

                <a href="{{ url('/produk') }}" class="btn btn-outline-td">
                    Lihat Semua
                </a>

            </div>


            <!-- PRODUCT LIST -->
            <div class="row g-4">
                @forelse ($products as $product)
                    @php
                        $lowestPrice = $product->variants->min('price');
                        $hasVariant = $product->variants->isNotEmpty();
                        $hasStock = $product->variants->sum('stock') > 0;
                        $productUrl = url('produk/' . $product->category->slug . '/' . $product->slug);
                        $branches = $product->variants
                            ->whereNotNull('branch_id')
                            ->map(fn($v) => $v->branch?->name)
                            ->filter()
                            ->unique()
                            ->values();
                    @endphp

                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="td-product-card h-100">

                            <!-- IMAGE -->
                            <a href="{{ $productUrl }}" class="td-product-thumb">
                                @if ($product->mainImage?->path)
                                    <img src="{{ asset('storage/' . $product->mainImage->path) }}">
                                @else
                                    <img src="{{ asset('images/no-image.png') }}">
                                @endif

                                @if (!$hasStock)
                                    <span class="td-badge bg-danger">Stok Habis</span>
                                @elseif ($product->is_new)
                                    <span class="td-badge bg-success">Baru</span>
                                @endif
                            </a>

                            <!-- BODY -->
                            <div class="td-product-body">

                                {{-- UMKM & Branch --}}
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    {{-- UMKM (kiri) --}}
                                    @if ($product->umkm)
                                        <span class="badge px-2 py-1"
                                            style="background: rgba(99,102,241,0.2); color: #a5b4fc; font-size: 0.7rem; border: 1px solid rgba(99,102,241,0.4);">
                                            <i class="fa-solid fa-store me-1"></i>{{ $product->umkm->name }}
                                        </span>
                                    @else
                                        <span></span>
                                    @endif

                                    {{-- Branch (kanan) --}}
                                    @if ($branches->isNotEmpty())
                                        <span class="badge px-2 py-1"
                                            style="background: rgba(16,185,129,0.15); color: #6ee7b7; font-size: 0.65rem; border: 1px solid rgba(16,185,129,0.3);">
                                            <i class="fa-solid fa-location-dot me-1"></i>{{ $branches->first() }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ $productUrl }}" class="td-product-title">
                                    {{ $product->name }}
                                </a>

                                <div class="td-price-row">
                                    <div class="td-price">
                                        <span class="td-price-now">
                                            Rp {{ number_format($lowestPrice ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- BUTTON -->
                                <div class="d-flex gap-2 mt-3">
                                    @if ($hasVariant)
                                        <a href="{{ $productUrl }}" class="btn btn-td w-100 td-btn-action">
                                            <i class="fa-solid fa-eye"></i>
                                            <span>Detail</span>
                                        </a>
                                    @else
                                        <button class="btn btn-secondary w-100" disabled>
                                            <i class="fa-solid fa-ban"></i>
                                            <span>Tidak Tersedia</span>
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                @empty
                    <div class="col-12">
                        <div class="td-card p-5 text-center">
                            <div class="mb-3" style="font-size:48px;">📦</div>
                            <h5 class="fw-semibold text-white mb-2">
                                Tidak ada produk yang ditampilkan
                            </h5>
                            <div class="td-subtitle">
                                Produk belum tersedia atau sedang dinonaktifkan.
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>


            <!-- CTA -->
            <div class="td-cta p-4 p-md-5 mt-5">

                <div class="content d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">

                    <div>
                        <h3 class="fw-bold mb-1">
                            Siap naik level bareng Trendora?
                        </h3>

                        <p class="td-subtitle mb-0">
                            Bangun katalog yang cepat, rapi, dan gampang dikelola.
                        </p>
                    </div>

                    <a href="{{ route('login') }}" class="btn btn-light btn-lg td-btn-cta">
                        <i class="fa-solid fa-rocket"></i>
                        <span>Mulai Sekarang</span>
                    </a>

                </div>

            </div>

        </div>

    </section>
@endsection
