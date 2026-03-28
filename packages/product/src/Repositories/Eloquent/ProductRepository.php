<?php

namespace ECommerce\Product\Repositories\Eloquent;

use ECommerce\Core\Repositories\BaseRepository;
use ECommerce\Product\Models\Product;
use ECommerce\Product\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->where('slug', $slug)->with(['category', 'variants', 'attributes', 'inventory'])->first();
    }

    public function findByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('category_id', $categoryId)
            ->active()
            ->with(['category', 'inventory'])
            ->paginate($perPage);
    }

    public function getActive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->active()->with(['category', 'inventory'])->paginate($perPage);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->active()
            ->with(['category', 'inventory'])
            ->paginate($perPage);
    }
}
