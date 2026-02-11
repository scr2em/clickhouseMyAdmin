<?php

namespace App\Providers;

use App\Services\ClickHouseService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ClickHouseService::class, function () {
            return new ClickHouseService();
        });
    }

    public function boot(): void
    {
        //
    }
}
