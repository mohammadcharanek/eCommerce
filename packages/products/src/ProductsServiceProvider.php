<?php

namespace Ecommerce\Products;

use Illuminate\Support\ServiceProvider;
use Ecommerce\Products\Services\ProductService;

class ProductsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/products.php',
            'products'
        );

        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'products');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/products.php' => config_path('products.php'),
            ], 'products-config');

            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'products-migrations');

            $this->publishes([
                __DIR__ . '/../database/seeders/' => database_path('seeders'),
            ], 'products-seeders');

            $this->publishes([
                __DIR__ . '/../resources/views/' => resource_path('views/vendor/products'),
            ], 'products-views');
        }
    }
}
