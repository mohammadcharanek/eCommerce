<?php

namespace ECommerce\Inventory\Services;

use ECommerce\Core\Services\BaseService;
use ECommerce\Inventory\Models\Inventory;
use ECommerce\Inventory\Repositories\Interfaces\InventoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService extends BaseService
{
    public function __construct(InventoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function updateStock(int $productId, int $quantity, ?int $variantId = null): Inventory
    {
        return $this->repository->updateStock($productId, $quantity, $variantId);
    }

    public function decreaseStock(int $productId, int $quantity, ?int $variantId = null): bool
    {
        return DB::transaction(function () use ($productId, $quantity, $variantId) {
            $inventory = Inventory::where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->lockForUpdate()
                ->first();

            if (!$inventory || ($inventory->quantity - $inventory->reserved_quantity) < $quantity) {
                return false;
            }

            $inventory->decrement('quantity', $quantity);
            return true;
        });
    }

    public function increaseStock(int $productId, int $quantity, ?int $variantId = null): bool
    {
        return DB::transaction(function () use ($productId, $quantity, $variantId) {
            $inventory = Inventory::where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                return false;
            }

            $inventory->increment('quantity', $quantity);
            return true;
        });
    }

    public function checkAvailability(int $productId, int $quantity, ?int $variantId = null): bool
    {
        return $this->repository->checkAvailability($productId, $quantity, $variantId);
    }

    public function getLowStock(): Collection
    {
        return $this->repository->getLowStock();
    }
}
