<?php

namespace App\Modules\Order\Repositories\Interfaces;

use App\Core\Contracts\RepositoryInterface;
use App\Modules\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface extends RepositoryInterface
{
    public function findByOrderNumber(string $orderNumber): ?Order;
    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator;
}
