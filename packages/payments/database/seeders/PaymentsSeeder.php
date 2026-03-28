<?php

namespace Ecommerce\Payments\Database\Seeders;

use Ecommerce\Orders\Models\Order;
use Ecommerce\Payments\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentsSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::doesntHave('payment')
            ->whereIn('status', [Order::STATUS_PROCESSING, Order::STATUS_DELIVERED])
            ->get();

        foreach ($orders as $order) {
            Payment::create([
                'order_id'         => $order->id,
                'transaction_id'   => 'TXN-' . strtoupper(uniqid()),
                'payment_method'   => Payment::METHOD_CREDIT_CARD,
                'status'           => Payment::STATUS_PAID,
                'amount'           => $order->total,
                'currency'         => $order->currency,
                'gateway_response' => ['seeded' => true],
                'paid_at'          => now(),
            ]);
        }
    }
}
