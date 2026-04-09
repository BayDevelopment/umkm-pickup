<?php

use App\Http\Controllers\AdminProdukController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResetPasswordController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Route::get('/', [HomeController::class, 'index']);
Route::middleware('redirect.dashboard')->get('/', [HomeController::class, 'index']);
Route::middleware('redirect.dashboard')->get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::middleware('redirect.dashboard')->get('/produk/{category}/{product}', [HomeController::class, 'show'])->name('products.detail');

// CART
Route::prefix('cart')
    ->middleware('redirect.dashboard')
    ->group(function () {

        Route::get('/', [CartController::class, 'index'])
            ->name('cart.index');

        Route::post('/add', [CartController::class, 'add'])
            ->name('cart.add');

        Route::post('/update/{id}', [CartController::class, 'update'])
            ->name('cart.update');

        Route::delete('/remove/{id}', [CartController::class, 'remove'])
            ->name('cart.remove');
    });

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Guest Only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->prefix('auth')->group(function () {

    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProses'])
        ->middleware('throttle:5,1')
        ->name('login.proses'); // max 5 percobaan per 1 menit

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'RegisterProses'])
        ->middleware('throttle:3,5')
        ->name('register.proses');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:3,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->middleware('throttle:3,1')
        ->name('password.update');
});

Route::post('/auth/logout', [AuthController::class, 'logoutProses'])
    ->middleware('auth')
    ->name('logout.proses');
Route::get('/auth/logout', function () {
    return redirect('/');
});



/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION
|--------------------------------------------------------------------------
*/

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {

    if (! $request->hasValidSignature()) {
        abort(403, 'Invalid or expired verification link.');
    }

    $user = User::findOrFail($id);

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    // ❌ JANGAN LOGIN DI SINI
    // ❌ JANGAN MASUK DASHBOARD

    return redirect()->route('login')
        ->with('success', 'Email berhasil diverifikasi 🎉 Silakan login.');
})->middleware('signed')->name('verification.verify');


Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Link verifikasi dikirim ulang.');
})->middleware('auth')->name('verification.send');


/*
|--------------------------------------------------------------------------
| CUSTOMER AREA (WAJIB LOGIN + ROLE CUSTOMER)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/product', [ProductController::class, 'productsCustomer'])
            ->name('product');
        Route::get('/produk/{category}/{product}', [ProductController::class, 'show'])
            ->name('detail.produk');

        Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan');

        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders');

        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');

        Route::get('/orders/status/all', [OrderController::class, 'statusAll'])
            ->name('orders.status.all');

        Route::post(
            '/orders/{order}/pay',
            [OrderController::class, 'uploadProof']
        )->name('orders.upload');

        Route::get('/laporan-saya', [LaporanController::class, 'index'])
            ->name('customer.laporan');

        Route::get('/laporan/export', [LaporanController::class, 'exportPdf'])
            ->name('laporan.export');
        /*
        |--------------------------------------------------------------------------
        | CUSTOMER CART
        |--------------------------------------------------------------------------
        */
        Route::prefix('cart')
            ->name('cart.')
            ->group(function () {

                Route::get('/', [CartController::class, 'indexCustomer'])
                    ->name('index');

                Route::post('/add', [CartController::class, 'addCustomer'])
                    ->name('add');

                Route::post('/update/{id}', [CartController::class, 'updateCustomer'])
                    ->name('update');

                Route::delete('/remove/{id}', [CartController::class, 'removeCustomer'])
                    ->name('remove');
            });

        // ✅ CHECKOUT (HARUS DI LUAR CART)
        // Group untuk user yang sudah login & verified
        Route::middleware(['auth', 'verified'])->group(function () {

            // Checkout normal (dari keranjang)
            Route::get('/checkout', [CheckoutController::class, 'index'])
                ->name('checkout');

            Route::post('/checkout', [CheckoutController::class, 'store'])
                ->name('checkout.store');

            // Beli Sekarang langsung (opsional, kalau mau pisah logic)
            Route::post('/buy-now', [CheckoutController::class, 'buyNow'])
                ->name('buy.now');
        });
    });
