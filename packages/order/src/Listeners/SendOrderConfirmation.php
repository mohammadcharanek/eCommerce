<?php

namespace ECommerce\Order\Listeners;

use ECommerce\Order\Events\OrderPlaced;
use ECommerce\Order\Jobs\SendOrderConfirmationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderConfirmation implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        SendOrderConfirmationEmail::dispatch($event->order);
    }
}
