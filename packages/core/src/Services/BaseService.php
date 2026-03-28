<?php

namespace ECommerce\Core\Services;

use ECommerce\Core\Contracts\RepositoryInterface;
use ECommerce\Core\Contracts\ServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService implements ServiceInterface
{
    public function __construct(protected RepositoryInterface $repository) {}

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getById(int|string $id): ?Model
    {
        return $this->repository->find($id);
    }
}
