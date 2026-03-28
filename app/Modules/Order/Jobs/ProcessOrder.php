<?php

namespace App\Modules\Order\Jobs;

use App\Modules\Order\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(public readonly Order $order) {}

    public function handle(): void
    {
        Log::info('Processing order: ' . $this->order->order_number);

        // Additional order processing logic: fraud check, warehouse notification, etc.
        $this->order->update(['status' => 'processing']);

        Log::info('Order processed: ' . $this->order->order_number);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to process order: ' . $this->order->order_number . ' Error: ' . $exception->getMessage());
    }
}
