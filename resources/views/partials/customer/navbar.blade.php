<nav class="navbar navbar-expand-lg fixed-top td-navbar">
    <div class="container d-flex align-items-center justify-content-between">

        {{-- BRAND --}}
        <a class="navbar-brand td-brand" href="{{ url('/') }}">
            <span class="trendora">Trendora</span><span class="pick">Pick</span>
        </a>
        <style>
            .td-brand {
                font-family: 'Poppins', sans-serif;
                font-size: 24px;
            }

            .trendora {
                font-style: italic;
                font-weight: 300;
                /* semi bold */
            }

            .pick {
                font-weight: 600;
                font-style: italic;
            }
        </style>

        {{-- RIGHT SIDE (MOBILE CART + TOGGLER) --}}
        <div class="d-flex align-items-center gap-3">

            {{-- CART MOBILE --}}
            @php
                $cartCount = 0;

                if (auth()->check()) {
                    $cart = \App\Models\CartModel::where('user_id', auth()->id())->first();
                } else {
                    $cart = \App\Models\CartModel::where('session_id', session()->getId())->first();
                }

                if ($cart) {
                    $cartCount = $cart->items()->count();
                }

            @endphp

            <a href="{{ route('customer.cart.index') }}" class="td-cart-mobile d-lg-none position-relative">

                <i class="fa-solid fa-cart-shopping"></i>

                @if ($cartCount > 0)
                    <span class="td-cart-badge">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>

            {{-- TOGGLER --}}
            <button class="navbar-toggler td-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        {{-- COLLAPSE MENU --}}
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">

                <li class="nav-item">
                    <a class="nav-link nav-underline {{ $navlink === 'dashboard' ? 'active' : '' }}"
                        href="{{ url('/customer/dashboard') }}">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-underline {{ $navlink === 'produk' ? 'active' : '' }}"
                        href="{{ url('/customer/product') }}">
                        Product
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-underline {{ $navlink === 'laporan' ? 'active' : '' }}"
                        href="{{ url('/customer/laporan') }}">
                        Laporan
                    </a>
                </li>

                {{-- CART DESKTOP --}}
                <li class="nav-item d-none d-lg-block">
                    <a href="{{ route('customer.cart.index') }}" class="td-cart-link position-relative">

                        <i class="fa-solid fa-cart-shopping"></i>

                        @if ($cartCount > 0)
                            <span class="td-cart-badge">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </li>

                {{-- USER DROPDOWN --}}
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center gap-2 td-user-toggle" href="#" role="button"
                        data-bs-toggle="dropdown">

                        {{-- Avatar --}}
                        <div class="td-user-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        {{-- Nama & Email hanya tampil di mobile (d-lg-none) --}}
                        <div class="d-flex flex-column ms-2 d-lg-none">
                            <span class="text-white fw-semibold small">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="text-secondary small">
                                {{ Auth::user()->email }}
                            </span>
                        </div>

                        {{-- Nama & Email hanya tampil di desktop (d-none d-lg-flex) --}}
                        <div class="d-none d-lg-flex flex-column ms-2">
                            <span class="text-white fw-semibold small">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="text-secondary small">
                                {{ Auth::user()->email }}
                            </span>
                        </div>

                        <i class="fa-solid fa-chevron-down td-user-caret d-none d-md-inline"></i>
                    </a>

                    {{-- Dropdown tanpa info user, hanya menu --}}
                    <ul class="dropdown-menu dropdown-menu-end td-user-dropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('customer.profile') }}">
                                <i class="fa-regular fa-user me-2"></i>
                                Profile
                            </a>
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout.proses') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
