<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Periksa apakah pengguna memiliki langganan aktif
        if (!$user || !$user->hasActiveSubscription()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Langganan Anda telah berakhir. Silakan perpanjang paket untuk melanjutkan.'
                ], 403);
            }

            // Redirect ke halaman paket untuk perpanjang langganan
            return redirect()->route('subscription.packages')
                ->with('subscriptionError', 'Langganan Anda telah berakhir. Silakan perpanjang paket untuk melanjutkan.');
        }

        return $next($request);
    }
}
