<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PermissionService;
use App\Services\AuthenticationService;
use App\Services\OtpService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services as singletons
        $this->app->singleton(PermissionService::class);
        $this->app->singleton(AuthenticationService::class);
        $this->app->singleton(OtpService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
