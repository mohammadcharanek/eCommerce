<?php

namespace App\Modules\Order\Providers;

use App\Modules\Order\Events\OrderPlaced;
use App\Modules\Order\Events\OrderStatusUpdated;
use App\Modules\Order\Listeners\NotifyAdminOnOrder;
use App\Modules\Order\Listeners\SendOrderConfirmation;
use App\Modules\Order\Listeners\UpdateInventoryOnOrder;
use App\Modules\Order\Repositories\Eloquent\OrderRepository;
use App\Modules\Order\Repositories\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Services\OrderService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderService::class);
    }

    public function boot(): void
    {
        Event::listen(OrderPlaced::class, SendOrderConfirmation::class);
        Event::listen(OrderPlaced::class, UpdateInventoryOnOrder::class);
        Event::listen(OrderPlaced::class, NotifyAdminOnOrder::class);
    }
}
