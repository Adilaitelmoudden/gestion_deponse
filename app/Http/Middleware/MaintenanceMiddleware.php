<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $settings = Cache::get('admin_system_settings', []);
        $maintenanceMode = $settings['maintenance_mode'] ?? false;

        if ($maintenanceMode) {
            // Allow admins to pass through
            $userRole = session('user_role');
            if ($userRole === 'admin') {
                return $next($request);
            }

            // Allow login/logout routes so users can still log in as admin
            if ($request->routeIs('login') || $request->routeIs('logout')) {
                return $next($request);
            }

            $message = $settings['maintenance_message'] ?? 'Le site est en maintenance. Merci de revenir plus tard.';

            // If AJAX/JSON request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $message, 'maintenance' => true], 503);
            }

            // Show maintenance page
            return response()->view('maintenance', compact('message'), 503);
        }

        return $next($request);
    }
}
