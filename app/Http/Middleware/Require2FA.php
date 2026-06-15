<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Require2FA
{
    public function handle(Request $request, Closure $next): Response
    {
        // If already verified with 2FA, continue
        if (session()->has('2fa_verified')) {
            return $next($request);
        }

        // If user is logged in but hasn't verified 2FA
        if (auth()->check() && auth()->user()->two_factor_enabled) {
            // Allow access to 2FA routes
            if ($request->routeIs('two-factor.*')) {
                return $next($request);
            }

            // Redirect to 2FA verification
            return redirect()->route('two-factor.show');
        }

        return $next($request);
    }
}
