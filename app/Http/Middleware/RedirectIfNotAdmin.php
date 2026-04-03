<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAdmin
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->withErrors(['error' => 'Access denied. You must log in to continue.']);
        }

        if (!in_array(Auth::user()->role, ['global_admin', 'admin', 'cashier'])) {
            return redirect()->route('home')->withErrors(['error' => 'Access denied. You do not have permission to access admin pages.']);
        }

        return $next($request);
    }
}
