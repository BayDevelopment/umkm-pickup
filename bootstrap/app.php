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

            if ($e->getStatusCode() === 403 && $request->is('admin*')) {
                $user = Auth::user();

                // Jika pending, jangan logout — redirect ke pending-approval
                if ($user && $user->status === 'pending') {
                    return redirect('/admin/pending-approval');
                }

                // Jika bukan admin sama sekali
                if (Auth::check()) {
                    Auth::logout();
                }

                return redirect('/admin/login')
                    ->with('error', 'Anda bukan admin.');
            }

            return null;
        });
    })->create();
