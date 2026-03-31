<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
        // HTTPS zorunluluğu (production ortamında)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Rate Limiting Tanımları
        $this->configureRateLimiting();
    }

    /**
     * Rate limiting kurallarını tanımla
     */
    protected function configureRateLimiting(): void
    {
        // Kapsül oluşturma: Dakikada 5, saatte 50
        RateLimiter::for('capsule-create', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->user()?->id ?: $request->ip()),
                Limit::perHour(50)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        // Kapsül görüntüleme: Dakikada 60
        RateLimiter::for('capsule-view', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Genel API: Dakikada 100
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });

        // Auth işlemleri: Dakikada 5 deneme (brute-force koruması)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
