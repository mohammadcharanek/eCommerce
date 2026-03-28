<?php

namespace ECommerce\Payment\Jobs;

use ECommerce\Order\Models\Order;
use ECommerce\Payment\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly Order $order,
        public readonly array $paymentData,
        public readonly string $gateway = 'stripe',
    ) {}

    public function handle(PaymentService $paymentService): void
    {
        Log::info('Processing payment for order: ' . $this->order->order_number);
        $paymentService->processPayment($this->order, $this->paymentData, $this->gateway);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Payment job failed for order: ' . $this->order->order_number . ' Error: ' . $exception->getMessage());
    }
}
