<?php

namespace ECommerce\Product\Providers;

use ECommerce\Product\Events\ProductCreated;
use ECommerce\Product\Listeners\SendProductNotification;
use ECommerce\Product\Repositories\Eloquent\ProductRepository;
use ECommerce\Product\Repositories\Interfaces\ProductRepositoryInterface;
use ECommerce\Product\Services\ProductService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/product.php', 'product');

        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        $this->publishes([
            __DIR__ . '/../../config/product.php' => config_path('product.php'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'ecommerce-product');

        Event::listen(ProductCreated::class, SendProductNotification::class);
    }
}
