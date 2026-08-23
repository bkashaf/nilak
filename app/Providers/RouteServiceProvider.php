<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * مسیر پیش‌فرض
     */
    public const HOME = '/';

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->routes(function () {

            // مسیرهای API
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // مسیرهای وب
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
