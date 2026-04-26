<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserApproval
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Bypass semua Livewire & AJAX request
        if (
            $request->ajax() ||
            $request->wantsJson() ||
            $request->is('livewire/*') ||
            $request->header('X-Livewire')
        ) {
            return $next($request);
        }

        // Jika active, jangan bisa ke pending-approval
        if ($user->status === 'active' && $request->is('admin/pending-approval')) {
            return response()->redirectTo('/admin');
        }

        // Jika pending, hanya boleh akses halaman tertentu
        if ($user->status === 'pending') {
            if (
                $request->is('admin/pending-approval') ||
                $request->is('admin/logout') ||
                $request->is('admin/email-verification*') ||
                $request->routeIs('filament.admin.auth.*') ||
                $request->is('admin/u-m-k-m-s/create')
            ) {
                return $next($request);
            }

            return response()->redirectTo('/admin/pending-approval');
        }

        return $next($request);
    }
}
