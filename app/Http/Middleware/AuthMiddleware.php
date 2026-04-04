<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter d\'abord.');
        }

        return $next($request);
    }
}