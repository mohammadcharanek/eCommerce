<?php

namespace App\Modules\Order\Listeners;

use App\Modules\Order\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UpdateInventoryOnOrder implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        // Inventory is already decremented during order creation in OrderService
        // This listener handles any additional post-order inventory logic
        Log::info('Order placed, inventory updated for order: ' . $event->order->order_number);
    }
}
