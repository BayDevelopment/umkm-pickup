<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index()
    {
        return view('pages.login', [
            'title' => 'Login | Fashion & Lifestyle',
        ]);
    }

    public function loginProses(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:100',
            'password' => 'required|max:100',
        ]);

        $email = Str::lower($request->email);
        $ip = $request->ip();

        // key unik untuk rate limit
        $key = "login:{$email}|{$ip}";

        // 🔥 cek terlalu banyak percobaan
        if (RateLimiter::tooManyAttempts($key, 5)) {

            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
            ])->withInput();
        }

        // ambil session guest sebelum login
        $oldSessionId = $request->session()->getId();

        // cek login
        if (!Auth::attempt($request->only('email', 'password'))) {

            // tambah hit rate limiter
            RateLimiter::hit($key, 60);

            // delay anti brute force
            sleep(1);

            return back()->withErrors([
                'email' => 'Email atau password salah.'
            ])->withInput();
        }

        // clear limiter jika sukses
        RateLimiter::clear($key);

        $user = Auth::user();

        // cek email verified
        if (is_null($user->email_verified_at)) {

            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun belum diverifikasi.'
            ]);
        }

        // regenerate session (ANTI SESSION HIJACK)
        $request->session()->regenerate();

        // merge cart guest
        $this->mergeCartAfterLogin($oldSessionId);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Login berhasil 👋');
    }

    private function mergeCartAfterLogin($oldSessionId)
    {
        $guestCart = \App\Models\CartModel::with('items')
            ->where('session_id', $oldSessionId)
            ->first();

        if (!$guestCart) return;

        $userId = Auth::id();

        $userCart = \App\Models\CartModel::with('items')
            ->where('user_id', $userId)
            ->first();

        if ($userCart) {

            foreach ($guestCart->items as $item) {

                $existing = $userCart->items()
                    ->where('product_variant_id', $item->product_variant_id)
                    ->first();

                if ($existing) {

                    $existing->quantity += $item->quantity;
                    $existing->save();
                } else {

                    $item->update([
                        'cart_id' => $userCart->id
                    ]);
                }
            }

            $guestCart->delete();
        } else {

            $guestCart->update([
                'user_id' => $userId,
                'session_id' => null
            ]);
        }
    }

    // REGISTER
    public function register()
    {
        return view('pages.register', [
            'title' => 'Register | Fashion & Lifestyle',
        ]);
    }

    public function RegisterProses(Request $request)
    {
        $messages = [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'email.unique' => 'Email sudah terdaftar.',

            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.max' => 'Password maksimal 100 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
        ];

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:100',
                'regex:/[a-z]/',   // huruf kecil
                'regex:/[A-Z]/',   // huruf besar
                'regex:/[0-9]/'    // angka
            ],
        ], $messages);

        $ip = $request->ip();
        $key = "register:{$ip}";

        // limit register
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Terlalu banyak pendaftaran. Coba lagi dalam {$seconds} detik."
            ])->withInput();
        }

        RateLimiter::hit($key, 300);

        // delay anti bot
        sleep(1);

        $user = User::create([
            'name' => strip_tags($request->name),
            'email' => Str::lower($request->email),
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        // kirim email verification
        $user->sendEmailVerificationNotification();

        return redirect()->route('login')
            ->with('success', 'Link verifikasi telah dikirim ke email Anda.');
    }

    // LOGOUT
    public function logoutProses(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil logout.');
    }
}
