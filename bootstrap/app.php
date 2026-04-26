<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__ . '/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
            'redirect.dashboard' => \App\Http\Middleware\RedirectIfAuthenticatedToDashboard::class,
            'check.approval' => \App\Http\Middleware\CheckUserApproval::class, // tambah ini
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpException $e, $request) {

            // Jika request dari Livewire/AJAX — jangan redirect, biarkan Filament handle sendiri
            if ($request->ajax() || $request->wantsJson() || $request->is('livewire/*')) {
                return null;
            }

            if ($e->getStatusCode() === 403 && $request->is('admin*')) {
                $user = Auth::user();

                if ($user && $user->status === 'pending') {
                    return response()->redirectTo('/admin/pending-approval');
                }

                if ($user && $user->role === 'owner') {
                    return response()->redirectTo('/admin/products');
                }

                if (Auth::check()) {
                    Auth::logout();
                }

                return response()->redirectTo('/admin/login')
                    ->with('error', 'Anda bukan admin.');
            }

            return null;
        });
    })->create();
