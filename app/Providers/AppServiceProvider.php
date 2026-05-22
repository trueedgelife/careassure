<?php

namespace App\Providers;

use App\Support\ActingContext;
use App\Support\TenantContext;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
        $this->app->singleton(ActingContext::class);
    }

    public function boot(): void
    {
        //
    }
}
