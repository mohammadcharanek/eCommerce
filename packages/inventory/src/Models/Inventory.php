<?php

namespace ECommerce\Inventory\Models;

use ECommerce\Product\Models\Product;
use ECommerce\Product\Models\ProductVariant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'product_id', 'variant_id', 'quantity', 'reserved_quantity',
        'warehouse', 'low_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'quantity'           => 'integer',
            'reserved_quantity'  => 'integer',
            'low_stock_threshold'=> 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function getAvailableQuantityAttribute(): int
    {
        return $this->quantity - $this->reserved_quantity;
    }

    public function isLowStock(): bool
    {
        return $this->available_quantity <= $this->low_stock_threshold;
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereRaw('(quantity - reserved_quantity) <= low_stock_threshold');
    }

    public function scopeForWarehouse(Builder $query, string $warehouse): Builder
    {
        return $query->where('warehouse', $warehouse);
    }
}
