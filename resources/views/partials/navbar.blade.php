<nav class="navbar navbar-expand-lg fixed-top td-navbar">
    <div class="container">
        <a class="navbar-brand td-brand" href="#">
            Trendora
        </a>

        <button class="navbar-toggler td-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

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

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 td-nav-icons">

                <li class="nav-item">
                    <a class="nav-link nav-underline {{ $navlink === 'beranda' ? 'active' : '' }}"
                        href="{{ url('/') }}">
                        Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-underline td-login-icon" href="{{ route('login.proses') }}" title="Login">
                        <i class="fa-regular fa-user"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('cart.index') }}" class="nav-link td-cart-link position-relative">

                        <i class="fa-solid fa-cart-shopping"></i>

                        @if ($cartCount > 0)
                            <span class="td-cart-badge">
                                {{ $cartCount }}
                            </span>
                        @endif

                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>
