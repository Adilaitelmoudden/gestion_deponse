<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user_id')) {
            // Always return JSON for assistant/chat route and any JSON/AJAX request
            if (
                $request->expectsJson() ||
                $request->ajax() ||
                $request->wantsJson() ||
                $request->is('assistant/chat') ||
                $request->is('*/chat')
            ) {
                return response()->json([
                    'reply' => '⚠️ انتهات جلستك. أعد تسجيل الدخول.',
                    'redirect' => route('login'),
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Veuillez vous connecter d\'abord.');
        }

        return $next($request);
    }
}
