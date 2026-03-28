<?php

namespace ECommerce\Cart\Providers;

use ECommerce\Cart\Services\CartService;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/cart.php', 'cart');

        $this->app->bind(CartService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        $this->publishes([
            __DIR__ . '/../../config/cart.php' => config_path('cart.php'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'ecommerce-cart');
    }
}
