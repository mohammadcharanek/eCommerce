<?php

namespace ECommerce\Order\Repositories\Eloquent;

use ECommerce\Core\Repositories\BaseRepository;
use ECommerce\Order\Models\Order;
use ECommerce\Order\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model->where('order_number', $orderNumber)->with(['items', 'payments', 'user'])->first();
    }

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->forUser($userId)
            ->with(['items', 'payments'])
            ->latest()
            ->paginate($perPage);
    }

    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byStatus($status)
            ->with(['items', 'user'])
            ->latest()
            ->paginate($perPage);
    }
}
