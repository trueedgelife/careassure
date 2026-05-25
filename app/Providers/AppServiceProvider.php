<?php

namespace App\Providers;

use App\Support\ActingContext;
use App\Support\TenantContext;
use Illuminate\Support\ServiceProvider;
use Filament\Tables\Columns\TextColumn;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
        $this->app->singleton(ActingContext::class);
    }

    public function boot(): void
    {
        TextColumn::configureUsing(fn (TextColumn $column) => $column->timezone('Europe/London'));
    }
}
