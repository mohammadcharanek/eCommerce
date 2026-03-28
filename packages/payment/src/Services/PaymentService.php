<?php

namespace ECommerce\Payment\Services;

use ECommerce\Order\Models\Order;
use ECommerce\Payment\Contracts\PaymentGatewayInterface;
use ECommerce\Payment\Events\PaymentProcessed;
use ECommerce\Payment\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private readonly PaymentGatewayInterface $gateway) {}

    public function processPayment(Order $order, array $paymentData, string $gatewayName = 'stripe'): Payment
    {
        return DB::transaction(function () use ($order, $paymentData, $gatewayName) {
            $payment = Payment::create([
                'order_id'  => $order->id,
                'gateway'   => $gatewayName,
                'amount'    => $order->total,
                'currency'  => $order->currency,
                'status'    => Payment::STATUS_PENDING,
            ]);

            $result = $this->gateway->charge($order->total, $order->currency, $paymentData);

            $status = $result['success'] ? Payment::STATUS_PAID : Payment::STATUS_FAILED;
            $payment->update([
                'transaction_id' => $result['transaction_id'] ?? null,
                'status'         => $status,
                'payload'        => $result['payload'] ?? [],
            ]);

            if ($result['success']) {
                $order->update(['status' => Order::STATUS_PROCESSING]);
            }

            event(new PaymentProcessed($payment, $result['success']));

            return $payment->fresh();
        });
    }

    public function refund(Payment $payment, float $amount): Payment
    {
        $result = $this->gateway->refund($payment->transaction_id, $amount);

        if ($result['success']) {
            $payment->update(['status' => Payment::STATUS_REFUNDED]);
            $payment->order->update(['status' => Order::STATUS_REFUNDED]);
        }

        return $payment->fresh();
    }

    public function getPaymentStatus(string $transactionId): string
    {
        return $this->gateway->getStatus($transactionId);
    }
}
