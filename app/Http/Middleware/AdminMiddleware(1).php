<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Accès non autorisé. Zone administrateur.');
        }

        return $next($request);
    }
}