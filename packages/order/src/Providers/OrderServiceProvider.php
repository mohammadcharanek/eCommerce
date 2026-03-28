<?php

namespace ECommerce\Order\Providers;

use ECommerce\Order\Events\OrderPlaced;
use ECommerce\Order\Events\OrderStatusUpdated;
use ECommerce\Order\Listeners\NotifyAdminOnOrder;
use ECommerce\Order\Listeners\SendOrderConfirmation;
use ECommerce\Order\Listeners\UpdateInventoryOnOrder;
use ECommerce\Order\Repositories\Eloquent\OrderRepository;
use ECommerce\Order\Repositories\Interfaces\OrderRepositoryInterface;
use ECommerce\Order\Services\OrderService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/order.php', 'order');

        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        $this->publishes([
            __DIR__ . '/../../config/order.php' => config_path('order.php'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'ecommerce-order');

        Event::listen(OrderPlaced::class, SendOrderConfirmation::class);
        Event::listen(OrderPlaced::class, UpdateInventoryOnOrder::class);
        Event::listen(OrderPlaced::class, NotifyAdminOnOrder::class);
    }
}
