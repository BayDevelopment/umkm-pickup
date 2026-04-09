@extends('layouts.auth')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">

                <div class="td-card p-4 p-md-5">
                    <!-- HEADER -->
                    <div class="text-center mb-4">
                        <div class="td-icon mx-auto mb-3">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-1">Buat Akun Baru</h4>
                        <p class="text-muted small mb-0">
                            Daftar untuk mulai belanja
                        </p>
                    </div>

                    <!-- FORM -->
                    <form method="POST" action="{{ route('register.proses') }}">
                        @csrf

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text td-input-icon">
                                    <i class="fa-regular fa-user"></i>
                                </span>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror" placeholder="Nama lengkap kamu"
                                    required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text td-input-icon">
                                    <i class="fa-regular fa-envelope"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror" placeholder="email@contoh.com"
                                    required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- PASSWORD -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text td-input-icon">
                                    <i class="fa-solid fa-lock"></i>
                                </span>

                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Minimal 8 karakter" required>

                                <!-- tombol mata -->
                                <span class="input-group-text toggle-password" data-target="password"
                                    style="cursor:pointer;">
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                            </div>

                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- CONFIRM PASSWORD -->
                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text td-input-icon">
                                    <i class="fa-solid fa-lock"></i>
                                </span>

                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    placeholder="Ulangi password" required>

                                <!-- tombol mata -->
                                <span class="input-group-text toggle-password" data-target="password_confirmation"
                                    style="cursor:pointer;">
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                            </div>

                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- BUTTON -->
                        <button type="submit" class="btn btn-td w-100 td-btn-action">
                            <i class="fa-solid fa-user-check"></i>
                            Daftar Sekarang
                        </button>
                    </form>

                    <!-- FOOTER -->
                    <div class="text-center mt-4">
                        <span class="small td-text-gradient" style="color: var(--td-primary);">
                            Sudah punya akun?
                        </span>
                        <a href="{{ route('login') }}" class="ms-1 small text-decoration-none" style="color:#d9c7ff;">
                            Login
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.replace("fa-eye", "fa-eye-slash");
                } else {
                    input.type = "password";
                    icon.classList.replace("fa-eye-slash", "fa-eye");
                }
            });
        });
    </script>
@endpush
