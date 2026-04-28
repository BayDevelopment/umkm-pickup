<footer style="background: #080e1a; border-top: 1px solid rgba(255,255,255,0.06);">

    {{-- MAIN FOOTER --}}
    <div class="container py-5">
        <div class="row g-5">

            {{-- BRAND --}}
            <div class="col-lg-4 col-md-6">
                <div class="mb-3">
                    <span
                        style="font-size: 26px; background: linear-gradient(135deg, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">

                        <span style="font-weight: 300; font-style: italic;">Trendora</span><span
                            style="font-weight: 450; font-style: italic;">Pick</span>

                    </span>
                </div>
                <p style="color: #64748b; font-size: 14px; line-height: 1.8; margin-bottom: 20px;">
                    Platform belanja produk UMKM lokal terpercaya. Temukan produk terbaik dari berbagai cabang dan ambil
                    langsung di tempat.
                </p>
                <div class="d-flex gap-2">
                    <a href="#"
                        style="width:36px; height:36px; background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.3); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#a5b4fc; text-decoration:none; transition:all 0.2s;"
                        onmouseover="this.style.background='rgba(99,102,241,0.3)'"
                        onmouseout="this.style.background='rgba(99,102,241,0.15)'">
                        <i class="fa-brands fa-instagram" style="font-size:14px;"></i>
                    </a>
                    <a href="#"
                        style="width:36px; height:36px; background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.3); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#a5b4fc; text-decoration:none; transition:all 0.2s;"
                        onmouseover="this.style.background='rgba(99,102,241,0.3)'"
                        onmouseout="this.style.background='rgba(99,102,241,0.15)'">
                        <i class="fa-brands fa-facebook" style="font-size:14px;"></i>
                    </a>
                    <a href="#"
                        style="width:36px; height:36px; background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.3); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#a5b4fc; text-decoration:none; transition:all 0.2s;"
                        onmouseover="this.style.background='rgba(99,102,241,0.3)'"
                        onmouseout="this.style.background='rgba(99,102,241,0.15)'">
                        <i class="fa-brands fa-tiktok" style="font-size:14px;"></i>
                    </a>
                    <a href="#"
                        style="width:36px; height:36px; background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.3); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#a5b4fc; text-decoration:none; transition:all 0.2s;"
                        onmouseover="this.style.background='rgba(99,102,241,0.3)'"
                        onmouseout="this.style.background='rgba(99,102,241,0.15)'">
                        <i class="fa-brands fa-whatsapp" style="font-size:14px;"></i>
                    </a>
                </div>
            </div>

            {{-- MENU --}}
            <div class="col-lg-2 col-md-6 col-6">
                <div
                    style="font-size:13px; font-weight:700; color:#e2e8f0; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">
                    Menu
                </div>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                    <li>
                        <a href="{{ url('/') }}"
                            style="color:#64748b; font-size:14px; text-decoration:none; transition:color 0.2s;"
                            onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#64748b'">
                            <i class="fa-solid fa-house me-2" style="font-size:11px;"></i>Beranda
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/produk') }}"
                            style="color:#64748b; font-size:14px; text-decoration:none; transition:color 0.2s;"
                            onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#64748b'">
                            <i class="fa-solid fa-bag-shopping me-2" style="font-size:11px;"></i>Produk
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/cart') }}"
                            style="color:#64748b; font-size:14px; text-decoration:none; transition:color 0.2s;"
                            onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#64748b'">
                            <i class="fa-solid fa-cart-shopping me-2" style="font-size:11px;"></i>Keranjang
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('customer.orders') }}"
                                style="color:#64748b; font-size:14px; text-decoration:none; transition:color 0.2s;"
                                onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#64748b'">
                                <i class="fa-solid fa-box me-2" style="font-size:11px;"></i>Pesanan Saya
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

            {{-- INFO --}}
            <div class="col-lg-3 col-md-6 col-6">
                <div
                    style="font-size:13px; font-weight:700; color:#e2e8f0; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">
                    Informasi
                </div>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                    <li>
                        <a href="#"
                            style="color:#64748b; font-size:14px; text-decoration:none; transition:color 0.2s;"
                            onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#64748b'">
                            <i class="fa-solid fa-shield-halved me-2" style="font-size:11px;"></i>Kebijakan Privasi
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            style="color:#64748b; font-size:14px; text-decoration:none; transition:color 0.2s;"
                            onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#64748b'">
                            <i class="fa-solid fa-file-contract me-2" style="font-size:11px;"></i>Syarat & Ketentuan
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            style="color:#64748b; font-size:14px; text-decoration:none; transition:color 0.2s;"
                            onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#64748b'">
                            <i class="fa-solid fa-circle-question me-2" style="font-size:11px;"></i>FAQ
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            style="color:#64748b; font-size:14px; text-decoration:none; transition:color 0.2s;"
                            onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#64748b'">
                            <i class="fa-solid fa-headset me-2" style="font-size:11px;"></i>Hubungi Kami
                        </a>
                    </li>
                </ul>
            </div>

            {{-- KONTAK --}}
            <div class="col-lg-3 col-md-6">
                <div
                    style="font-size:13px; font-weight:700; color:#e2e8f0; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">
                    Kontak
                </div>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div style="display:flex; align-items:flex-start; gap:10px;">
                        <i class="fa-solid fa-envelope" style="color:#6366f1; margin-top:2px; flex-shrink:0;"></i>
                        <span style="color:#64748b; font-size:14px;">trendora@gmail.com</span>
                    </div>
                    <div style="display:flex; align-items:flex-start; gap:10px;">
                        <i class="fa-solid fa-phone" style="color:#6366f1; margin-top:2px; flex-shrink:0;"></i>
                        <span style="color:#64748b; font-size:14px;">+62 813-9975-8951</span>
                    </div>
                    <div style="display:flex; align-items:flex-start; gap:10px;">
                        <i class="fa-solid fa-location-dot" style="color:#6366f1; margin-top:2px; flex-shrink:0;"></i>
                        <span style="color:#64748b; font-size:14px;">Cilegon, Banten, Indonesia</span>
                    </div>
                </div>

                {{-- Tagline --}}
                <div
                    style="margin-top:20px; background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.2); border-radius:10px; padding:12px 14px;">
                    <div style="font-size:11px; color:#6366f1; font-weight:700; margin-bottom:4px;">✨ TAGLINE</div>
                    <div style="font-size:13px; color:#a5b4fc; font-style:italic;">
                        "Belanja UMKM Lokal, Ambil di Tempat"
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- DIVIDER --}}
    <div style="border-top: 1px solid rgba(255,255,255,0.06);"></div>

    {{-- BOTTOM BAR --}}
    <div class="container py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">

            <div style="font-size:13px; color:#475569;">
                &copy; {{ date('Y') }} <span style="color:#6366f1; font-weight:700;">Trendora</span>. All rights
                reserved.
            </div>

            <div style="font-size:13px; color:#475569;">
                Developed with <span style="color:#ef4444;">❤️</span> by
                <a href="https://www.linkedin.com/in/bayu-albar-ladici-637781273/" target="_blank"
                    style="color:#a5b4fc; text-decoration:none; font-weight:600;"
                    onmouseover="this.style.color='#6366f1'" onmouseout="this.style.color='#a5b4fc'">
                    Bayu Albar Ladici
                </a>
            </div>

        </div>
    </div>

</footer>
