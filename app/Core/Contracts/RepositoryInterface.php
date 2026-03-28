<?php

namespace App\Core\Contracts;

interface RepositoryInterface
{
    public function all(array $columns = ['*']): \Illuminate\Database\Eloquent\Collection;
    public function find(int|string $id, array $columns = ['*']): ?\Illuminate\Database\Eloquent\Model;
    public function create(array $data): \Illuminate\Database\Eloquent\Model;
    public function update(int|string $id, array $data): \Illuminate\Database\Eloquent\Model;
    public function delete(int|string $id): bool;
    public function paginate(int $perPage = 15, array $columns = ['*']): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
