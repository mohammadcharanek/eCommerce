<?php

namespace Ecommerce\Products\Services;

use Ecommerce\Products\Models\Product;
use Ecommerce\Products\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    /**
     * Retrieve a paginated list of products.
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::with('category');

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_featured']) && $filters['is_featured']) {
            $query->featured();
        }

        if (isset($filters['in_stock']) && $filters['in_stock']) {
            $query->inStock();
        }

        $sortBy  = $filters['sort_by']  ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';

        $allowed = ['name', 'price', 'created_at', 'stock_quantity'];
        if (in_array($sortBy, $allowed)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update an existing product.
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    /**
     * Delete a product (soft delete).
     */
    public function delete(Product $product): void
    {
        $product->delete();
    }

    /**
     * Retrieve all active categories.
     */
    public function allCategories(): Collection
    {
        return Category::active()->orderBy('name')->get();
    }

    /**
     * Retrieve featured products.
     */
    public function featuredProducts(int $limit = 8): Collection
    {
        return Product::active()->featured()->inStock()
            ->with('category')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Search products by keyword.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return Product::active()
            ->with('category')
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%")
                      ->orWhere('sku', 'like', "%{$keyword}%");
            })
            ->paginate($perPage);
    }
}
