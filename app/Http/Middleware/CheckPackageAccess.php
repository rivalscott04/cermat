<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPackageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredAccess): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Periksa apakah pengguna memiliki langganan aktif
        if (!$user->hasActiveSubscription()) {
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

        // Periksa akses berdasarkan package
        $hasAccess = false;
        $errorMessage = '';

        switch ($requiredAccess) {
            case 'kecermatan':
                $hasAccess = $user->canAccessKecermatan();
                $errorMessage = 'Paket Anda tidak termasuk akses ke Tes Kecermatan. Upgrade ke paket Kecermatan atau Lengkap untuk mengakses fitur ini.';
                break;

            case 'tryout':
                $hasAccess = $user->canAccessTryout();
                $errorMessage = 'Paket Anda tidak termasuk akses ke Tryout CBT. Upgrade ke paket Psikologi atau Lengkap untuk mengakses fitur ini.';
                break;

            default:
                $hasAccess = false;
                $errorMessage = 'Akses tidak valid.';
        }

        if (!$hasAccess) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage
                ], 403);
            }

            // Redirect ke halaman paket untuk upgrade
            return redirect()->route('subscription.packages')
                ->with('packageError', $errorMessage);
        }

        return $next($request);
    }
}
