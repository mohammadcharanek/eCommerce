<?php

namespace Ecommerce\Products\Models;

use Ecommerce\Products\Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'cost_price',
        'stock_quantity',
        'low_stock_threshold',
        'sku',
        'category_id',
        'image',
        'images',
        'is_active',
        'is_featured',
        'weight',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'compare_price'       => 'decimal:2',
        'cost_price'          => 'decimal:2',
        'stock_quantity'      => 'integer',
        'low_stock_threshold' => 'integer',
        'images'              => 'array',
        'is_active'           => 'boolean',
        'is_featured'         => 'boolean',
        'weight'              => 'decimal:3',
    ];

    /**
     * Get the category this product belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include in-stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Check if the product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Check if stock is running low.
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold
            && $this->stock_quantity > 0;
    }

    /**
     * Decrement stock by a given quantity.
     */
    public function decrementStock(int $quantity): void
    {
        if ($this->stock_quantity < $quantity) {
            throw new \RuntimeException("Insufficient stock for product [{$this->id}].");
        }

        $this->decrement('stock_quantity', $quantity);
    }

    /**
     * Increment stock by a given quantity.
     */
    public function incrementStock(int $quantity): void
    {
        $this->increment('stock_quantity', $quantity);
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if (! $this->compare_price || $this->compare_price <= $this->price) {
            return null;
        }

        return round((($this->compare_price - $this->price) / $this->compare_price) * 100, 1);
    }
}
