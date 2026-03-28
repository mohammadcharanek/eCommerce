<?php

namespace ECommerce\Order\Jobs;

use ECommerce\Order\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly Order $order) {}

    public function handle(): void
    {
        $order = $this->order->load(['items', 'user']);

        Log::info('Sending order confirmation email for order: ' . $order->order_number . ' to ' . $order->user->email);

        // In production, use a Mailable class:
        // Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send order confirmation for: ' . $this->order->order_number . ' Error: ' . $exception->getMessage());
    }
}
