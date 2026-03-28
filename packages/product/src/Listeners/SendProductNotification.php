<?php

namespace ECommerce\Product\Listeners;

use ECommerce\Product\Events\ProductCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendProductNotification implements ShouldQueue
{
    public function handle(ProductCreated $event): void
    {
        // Notify relevant users about the new product
        // e.g., marketing team, subscribed customers
        \Illuminate\Support\Facades\Log::info('New product created: ' . $event->product->name);
    }
}
