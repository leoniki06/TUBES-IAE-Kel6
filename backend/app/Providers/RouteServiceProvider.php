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
     *
     * - Naikkan limit API supaya tidak gampang kena 429 "Too Many Attempts"
     * - Key by user_id (kalau login) atau by IP (kalau belum login)
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $key = optional($request->user())->id ?: $request->ip();

            // ✅ Dev-friendly: longgarin limit biar CRUD + search/pagination aman
            return Limit::perMinute(1000)->by($key);
        });

        // (opsional) kalau kamu punya limiter khusus login, bisa kamu define di sini juga.
        // RateLimiter::for('login', function (Request $request) {
        //     return Limit::perMinute(60)->by($request->ip());
        // });
    }
}
