@extends('layouts.customer')

@section('customer')
    <section class="td-page td-page--after-navbar" style="background:#0b1220;">
        <div class="container">

            <h3 class="text-white mb-4">Profile Saya</h3>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row g-4">

                {{-- LEFT COLUMN --}}
                <div class="col-lg-5">
                    <div class="profile-card p-4">

                        <h5 class="text-white mb-3">Informasi Akun</h5>

                        <div class="profile-item">
                            <label>Nama</label>
                            <div>{{ $user->name }}</div>
                        </div>

                        <div class="profile-item">
                            <label>Email</label>
                            <div>{{ $user->email }}</div>
                        </div>

                        <div class="profile-item">
                            <label>Role</label>
                            <div class="text-info text-capitalize">
                                {{ $user->role }}
                            </div>
                        </div>

                    </div>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-lg-7">
                    <div class="profile-card p-4">

                        <h5 class="text-white mb-3">Edit Profile</h5>

                        <form action="{{ route('customer.profile.update') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label text-white">Nama</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="form-control td-input">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="form-control td-input">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Role</label>
                                <input type="text" value="{{ $user->role }}"
                                    class="form-control td-input td-input-disabled" disabled>
                            </div>

                            <button type="submit" class="btn-modern">
                                Simpan Perubahan
                            </button>

                        </form>

                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection


@push('styles')
    <style>
        .profile-card {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 18px;
            transition: all .25s ease;
        }

        .profile-card:hover {
            border-color: rgba(255, 255, 255, .2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .4);
        }

        .profile-item {
            margin-bottom: 15px;
        }

        .profile-item label {
            font-size: 13px;
            color: #9ca3af;
            display: block;
        }

        .profile-item div {
            font-size: 15px;
            font-weight: 500;
            color: #fff;
        }

        .td-input {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            color: #fff;
            border-radius: 12px;
        }

        .td-input:focus {
            background: rgba(255, 255, 255, .08);
            border-color: #6366f1;
            box-shadow: none;
            color: #fff;
        }

        .btn-modern {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            color: #fff;
            font-weight: 600;
            transition: all .2s ease;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, .4);
        }

        @media (max-width: 768px) {
            .profile-card {
                margin-bottom: 20px;
            }
        }

        .td-input-disabled {
            background: rgba(255, 255, 255, .05) !important;
            border: 1px solid rgba(255, 255, 255, .1) !important;
            color: #9ca3af !important;
            opacity: 1 !important;
            /* penting supaya tidak pudar */
            cursor: not-allowed;
        }
    </style>
@endpush
