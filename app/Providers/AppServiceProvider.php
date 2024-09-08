<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MailConfigService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MailConfigService::class, function ($app) {
            return new MailConfigService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
