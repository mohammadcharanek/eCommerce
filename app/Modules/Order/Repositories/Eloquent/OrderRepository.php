<?php

namespace App\Modules\Order\Repositories\Eloquent;

use App\Core\Repositories\BaseRepository;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Repositories\Interfaces\OrderRepositoryInterface;
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
