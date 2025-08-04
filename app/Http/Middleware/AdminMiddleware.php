<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            // Check if user is being impersonated
            if (\Illuminate\Support\Facades\Session::has('impersonate_id')) {
                return redirect('/')->with('error', 'Tidak dapat mengakses halaman admin saat dalam mode impersonate.');
            }
            
            return $next($request);
        }

        return redirect('/')->with('error', 'Unauthorized access.');
    }
}