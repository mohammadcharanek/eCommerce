<?php

namespace ECommerce\Inventory\Repositories\Eloquent;

use ECommerce\Core\Repositories\BaseRepository;
use ECommerce\Inventory\Models\Inventory;
use ECommerce\Inventory\Repositories\Interfaces\InventoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    public function __construct(Inventory $model)
    {
        parent::__construct($model);
    }

    public function updateStock(int $productId, int $quantity, ?int $variantId = null): Inventory
    {
        $inventory = $this->model->firstOrCreate(
            ['product_id' => $productId, 'variant_id' => $variantId],
            ['quantity' => 0, 'reserved_quantity' => 0]
        );
        $inventory->update(['quantity' => $quantity]);
        return $inventory->fresh();
    }

    public function checkAvailability(int $productId, int $quantity, ?int $variantId = null): bool
    {
        $inventory = $this->model
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if (!$inventory) {
            return false;
        }

        return ($inventory->quantity - $inventory->reserved_quantity) >= $quantity;
    }

    public function getLowStock(): Collection
    {
        return $this->model->lowStock()->with(['product', 'variant'])->get();
    }
}
