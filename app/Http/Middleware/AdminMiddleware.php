<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        if ($user->banned_at) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Votre compte a été banni par l\'administrateur.');
        }

        if (!$user->is_global_admin) {
            return redirect('/dashboard')->with('error', 'Access denied. Réservé aux administrateurs.');
        }

        return $next($request); 
    }
}
