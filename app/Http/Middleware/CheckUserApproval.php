<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->status === 'pending') {
            // Izinkan akses ke halaman-halaman ini
            if (
                $request->is('admin/pending-approval') ||
                $request->is('admin/logout') ||
                $request->is('admin/email-verification*') ||
                $request->routeIs('filament.admin.auth.*')
            ) {
                return $next($request);
            }

            return redirect('/admin/pending-approval');
        }

        return $next($request);
    }
}
