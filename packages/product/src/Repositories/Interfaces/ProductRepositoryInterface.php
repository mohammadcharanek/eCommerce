<?php

namespace ECommerce\Product\Repositories\Interfaces;

use ECommerce\Core\Contracts\RepositoryInterface;
use ECommerce\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Product;
    public function findByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator;
    public function getActive(int $perPage = 15): LengthAwarePaginator;
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;
}
