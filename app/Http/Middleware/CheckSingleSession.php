<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSingleSession
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // Cek apakah session ID saat ini sama dengan yang tersimpan di database
            $currentSessionId = session()->getId();

            if ($user->session_id !== $currentSessionId) {
                // Logout user jika session berbeda
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Akun Anda telah login di perangkat lain.');
            }
        }

        return $next($request);
    }
}
