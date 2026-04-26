<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticatedToDashboard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {

            $user = Auth::user();

            if ($user->role === 'admin') {
                return response()->redirectTo('/admin'); // ✅ fix
            }

            if ($user->role === 'owner') {
                return response()->redirectTo('/admin'); // ✅ fix
            }

            if ($user->role === 'customer') {
                return response()->redirectTo(route('customer.dashboard')); // ✅ fix
            }

            return response()->redirectTo('/'); // ✅ fix
        }

        return $next($request);
    }
}
