<?php

namespace Ecommerce\Payments\Tests\Unit;

use Ecommerce\Orders\Models\Order;
use Ecommerce\Payments\Models\Payment;
use Ecommerce\Payments\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'order_number'    => Order::generateOrderNumber(),
            'status'          => Order::STATUS_PENDING,
            'subtotal'        => 100.00,
            'tax_amount'      => 10.00,
            'shipping_amount' => 5.00,
            'discount_amount' => 0.00,
            'total'           => 115.00,
            'currency'        => 'USD',
            'billing_name'    => 'Jane Doe',
            'billing_email'   => 'jane@example.com',
            'billing_address' => '456 Oak Ave',
            'billing_city'    => 'Springfield',
            'billing_zip'     => '62701',
            'billing_country' => 'US',
        ], $overrides));
    }

    public function test_payment_can_be_created(): void
    {
        $order   = $this->makeOrder();
        $payment = Payment::create([
            'order_id'       => $order->id,
            'payment_method' => Payment::METHOD_CREDIT_CARD,
            'status'         => Payment::STATUS_PAID,
            'amount'         => $order->total,
            'currency'       => 'USD',
            'paid_at'        => now(),
        ]);

        $this->assertDatabaseHas('payments', ['order_id' => $order->id]);
        $this->assertTrue($payment->isPaid());
    }

    public function test_paid_payment_is_refundable(): void
    {
        $order   = $this->makeOrder();
        $payment = Payment::make([
            'order_id'       => $order->id,
            'payment_method' => Payment::METHOD_PAYPAL,
            'status'         => Payment::STATUS_PAID,
            'amount'         => 115.00,
            'currency'       => 'USD',
        ]);

        $this->assertTrue($payment->isRefundable());
    }

    public function test_failed_payment_is_not_refundable(): void
    {
        $order   = $this->makeOrder();
        $payment = Payment::make([
            'order_id'       => $order->id,
            'payment_method' => Payment::METHOD_CREDIT_CARD,
            'status'         => Payment::STATUS_FAILED,
            'amount'         => 115.00,
            'currency'       => 'USD',
        ]);

        $this->assertFalse($payment->isRefundable());
    }

    public function test_paid_scope_returns_only_paid_payments(): void
    {
        $order = $this->makeOrder();

        Payment::create(['order_id' => $order->id, 'payment_method' => Payment::METHOD_CREDIT_CARD, 'status' => Payment::STATUS_PAID,   'amount' => 100, 'currency' => 'USD']);
        Payment::create(['order_id' => $order->id, 'payment_method' => Payment::METHOD_PAYPAL,      'status' => Payment::STATUS_FAILED, 'amount' => 100, 'currency' => 'USD']);

        $paid = Payment::paid()->get();

        $this->assertCount(1, $paid);
        $this->assertEquals(Payment::STATUS_PAID, $paid->first()->status);
    }

    public function test_service_provider_is_registered(): void
    {
        $this->assertInstanceOf(
            \Ecommerce\Payments\PaymentsServiceProvider::class,
            $this->app->getProvider(\Ecommerce\Payments\PaymentsServiceProvider::class)
        );
    }

    public function test_config_is_loaded(): void
    {
        $this->assertNotEmpty(config('payments.methods'));
    }
}
