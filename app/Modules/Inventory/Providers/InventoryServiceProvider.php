<?php

namespace App\Modules\Inventory\Providers;

use App\Modules\Inventory\Repositories\Eloquent\InventoryRepository;
use App\Modules\Inventory\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Modules\Inventory\Services\InventoryService;
use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(InventoryService::class);
    }
}
