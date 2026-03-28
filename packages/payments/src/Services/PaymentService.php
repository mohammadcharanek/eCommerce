<?php

namespace Ecommerce\Payments\Services;

use Ecommerce\Orders\Models\Order;
use Ecommerce\Payments\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Retrieve a paginated list of payments.
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Payment::with('order');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['payment_method'])) {
            $query->byMethod($filters['payment_method']);
        }

        if (! empty($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Process a new payment for an order.
     *
     * In a real integration, $gatewayData would contain the gateway's
     * response payload (e.g. from Stripe, PayPal, etc.).
     */
    public function process(Order $order, array $paymentData, array $gatewayData = []): Payment
    {
        return DB::transaction(function () use ($order, $paymentData, $gatewayData) {
            $payment = Payment::create([
                'order_id'         => $order->id,
                'transaction_id'   => $paymentData['transaction_id'] ?? null,
                'payment_method'   => $paymentData['payment_method'],
                'status'           => Payment::STATUS_PAID,
                'amount'           => $order->total,
                'currency'         => $order->currency,
                'gateway_response' => $gatewayData,
                'paid_at'          => now(),
            ]);

            $order->update(['status' => Order::STATUS_PROCESSING]);

            return $payment;
        });
    }

    /**
     * Mark a payment as failed.
     */
    public function markFailed(Order $order, array $paymentData, array $gatewayData = []): Payment
    {
        return Payment::create([
            'order_id'         => $order->id,
            'transaction_id'   => $paymentData['transaction_id'] ?? null,
            'payment_method'   => $paymentData['payment_method'],
            'status'           => Payment::STATUS_FAILED,
            'amount'           => $order->total,
            'currency'         => $order->currency,
            'gateway_response' => $gatewayData,
        ]);
    }

    /**
     * Refund a payment.
     */
    public function refund(Payment $payment, ?float $amount = null): Payment
    {
        if (! $payment->isRefundable()) {
            throw new \RuntimeException("Payment [{$payment->id}] is not refundable.");
        }

        $refundAmount = $amount ?? (float) $payment->amount;

        if ($refundAmount > (float) $payment->amount) {
            throw new \InvalidArgumentException('Refund amount cannot exceed the original payment amount.');
        }

        return DB::transaction(function () use ($payment, $refundAmount) {
            $payment->update([
                'status'        => Payment::STATUS_REFUNDED,
                'refund_amount' => $refundAmount,
                'refunded_at'   => now(),
            ]);

            $payment->order->update(['status' => Order::STATUS_REFUNDED]);

            return $payment->fresh();
        });
    }
}
