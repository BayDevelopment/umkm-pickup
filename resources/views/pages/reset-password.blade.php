@extends('layouts.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">

                <div class="td-card p-4">

                    <h4 class="text-white mb-3">Reset Password</h4>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">

                            <label>Email</label>

                            <input type="email" name="email" class="form-control" placeholder="Masukan email anda"
                                required>

                        </div>

                        <!-- PASSWORD BARU -->
                        <div class="mb-3 position-relative">
                            <label>Password Baru</label>

                            <input type="password" id="new_password" name="password" class="form-control pe-5"
                                placeholder="Password baru" required>

                            <!-- icon mata -->
                            <span class="toggle-password position-absolute top-50 end-0 translate-middle-y me-3"
                                data-target="new_password" style="cursor:pointer;">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>

                        <!-- KONFIRMASI PASSWORD -->
                        <div class="mb-3 position-relative">
                            <label>Konfirmasi Password</label>

                            <input type="password" id="confirm_password" name="password_confirmation"
                                class="form-control pe-5" placeholder="Konfirmasi password" required>

                            <!-- icon mata -->
                            <span class="toggle-password position-absolute top-50 end-0 translate-middle-y me-3"
                                data-target="confirm_password" style="cursor:pointer;">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>

                        <button class="btn btn-td w-100">
                            Reset Password
                        </button>

                    </form>

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
