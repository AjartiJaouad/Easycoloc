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

        if (!$user->is_global_admin) {
            return redirect('/dashboard')->with('error', 'Access denied.'); // user عادي
        }

        return $next($request); // user admin
    }
}
