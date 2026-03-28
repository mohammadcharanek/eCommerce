<?php

namespace Ecommerce\Payments;

use Illuminate\Support\ServiceProvider;
use Ecommerce\Payments\Services\PaymentService;

class PaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/payments.php',
            'payments'
        );

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'payments');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/payments.php' => config_path('payments.php'),
            ], 'payments-config');

            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'payments-migrations');

            $this->publishes([
                __DIR__ . '/../database/seeders/' => database_path('seeders'),
            ], 'payments-seeders');

            $this->publishes([
                __DIR__ . '/../resources/views/' => resource_path('views/vendor/payments'),
            ], 'payments-views');
        }
    }
}
