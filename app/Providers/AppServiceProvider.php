<?php

namespace App\Providers;

use App\Services\NotificationService;
use App\View\Composers\HeaderComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // NotificationService sebagai singleton → cache query per request
        $this->app->singleton(NotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS URL scheme saat diakses via HTTPS, di balik reverse proxy (Hostinger/Cloudflare), atau production
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https'
            || request()->isSecure()
            || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            || app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Attach HeaderComposer ke view header
        View::composer('layouts.header', HeaderComposer::class);
    }
}
