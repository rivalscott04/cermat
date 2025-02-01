<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Observers\SubscriptionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID');

        \App\Models\Subscription::observe(SubscriptionObserver::class);
    }
}
