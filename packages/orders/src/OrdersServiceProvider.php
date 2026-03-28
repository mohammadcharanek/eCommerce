<?php

namespace Ecommerce\Orders;

use Illuminate\Support\ServiceProvider;
use Ecommerce\Orders\Services\OrderService;

class OrdersServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/orders.php',
            'orders'
        );

        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'orders');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/orders.php' => config_path('orders.php'),
            ], 'orders-config');

            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'orders-migrations');

            $this->publishes([
                __DIR__ . '/../database/seeders/' => database_path('seeders'),
            ], 'orders-seeders');

            $this->publishes([
                __DIR__ . '/../resources/views/' => resource_path('views/vendor/orders'),
            ], 'orders-views');
        }
    }
}
