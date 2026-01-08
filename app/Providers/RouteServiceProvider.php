<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter untuk login - 5 attempts per 1 menit per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return redirect()->route('login')
                    ->with('error', 'Terlalu banyak percobaan login. Silakan coba lagi dalam 1 menit.');
            });
        });

        // Rate limiter untuk register - 5 attempts per 1 menit per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return redirect()->route('register')
                    ->with('error', 'Terlalu banyak percobaan pendaftaran. Silakan coba lagi dalam 1 menit.');
            });
        });

        // Rate limiter untuk reset password - 3 attempts per 1 jam per IP
        RateLimiter::for('reset-password', function (Request $request) {
            return Limit::perHour(3)->by($request->ip())->response(function () {
                return redirect()->route('reset-password')
                    ->with('error', 'Terlalu banyak percobaan reset password. Silakan coba lagi dalam 1 jam.');
            });
        });
    }
}
