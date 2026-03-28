<?php

namespace ECommerce\Core\Contracts;

interface ServiceInterface
{
    public function getAll(): \Illuminate\Database\Eloquent\Collection;
    public function getById(int|string $id): ?\Illuminate\Database\Eloquent\Model;
}
