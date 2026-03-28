<?php

namespace ECommerce\Order\Listeners;

use ECommerce\Order\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotifyAdminOnOrder implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        Log::info('New order placed: ' . $event->order->order_number . ' Total: ' . $event->order->total);
    }
}
