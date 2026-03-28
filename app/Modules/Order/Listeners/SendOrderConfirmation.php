<?php

namespace App\Modules\Order\Listeners;

use App\Modules\Order\Events\OrderPlaced;
use App\Modules\Order\Jobs\SendOrderConfirmationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderConfirmation implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        SendOrderConfirmationEmail::dispatch($event->order);
    }
}
