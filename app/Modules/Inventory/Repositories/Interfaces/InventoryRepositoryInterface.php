<?php

namespace App\Modules\Inventory\Repositories\Interfaces;

use App\Core\Contracts\RepositoryInterface;
use App\Modules\Inventory\Models\Inventory;
use Illuminate\Database\Eloquent\Collection;

interface InventoryRepositoryInterface extends RepositoryInterface
{
    public function updateStock(int $productId, int $quantity, ?int $variantId = null): Inventory;
    public function checkAvailability(int $productId, int $quantity, ?int $variantId = null): bool;
    public function getLowStock(): Collection;
}
