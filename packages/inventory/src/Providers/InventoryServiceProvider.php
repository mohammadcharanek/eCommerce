<?php

namespace ECommerce\Inventory\Providers;

use ECommerce\Inventory\Repositories\Eloquent\InventoryRepository;
use ECommerce\Inventory\Repositories\Interfaces\InventoryRepositoryInterface;
use ECommerce\Inventory\Services\InventoryService;
use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/inventory.php', 'inventory');

        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(InventoryService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        $this->publishes([
            __DIR__ . '/../../config/inventory.php' => config_path('inventory.php'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'ecommerce-inventory');
    }
}
