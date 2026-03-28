<?php

namespace App\Modules\Product\Services;

use App\Core\Services\BaseService;
use App\Modules\Product\Events\ProductCreated;
use App\Modules\Product\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductService extends BaseService
{
    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function listProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        if (!empty($filters['search'])) {
            return $this->repository->search($filters['search'], $perPage);
        }
        if (!empty($filters['category_id'])) {
            return $this->repository->findByCategory($filters['category_id'], $perPage);
        }
        return $this->repository->getActive($perPage);
    }

    public function getProduct(int|string $id): ?Model
    {
        if (!is_numeric($id)) {
            return $this->repository->findBySlug($id);
        }
        return $this->repository->find($id);
    }

    public function createProduct(array $data): Model
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']) . '-' . Str::random(6);
        $product = $this->repository->create($data);
        event(new ProductCreated($product));
        return $product;
    }

    public function updateProduct(int $id, array $data): Model
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);
        }
        return $this->repository->update($id, $data);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
