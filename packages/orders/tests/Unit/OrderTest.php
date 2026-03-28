<?php

namespace Ecommerce\Orders\Tests\Unit;

use Ecommerce\Orders\Models\Order;
use Ecommerce\Orders\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
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
            'billing_name'    => 'John Doe',
            'billing_email'   => 'john@example.com',
            'billing_address' => '123 Main St',
            'billing_city'    => 'Springfield',
            'billing_zip'     => '62701',
            'billing_country' => 'US',
        ], $overrides));
    }

    public function test_order_can_be_created(): void
    {
        $order = $this->makeOrder();

        $this->assertDatabaseHas('orders', ['billing_email' => 'john@example.com']);
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
    }

    public function test_order_number_is_generated_uniquely(): void
    {
        $n1 = Order::generateOrderNumber();
        $n2 = Order::generateOrderNumber();

        $this->assertNotEquals($n1, $n2);
        $this->assertStringStartsWith('ORD-', $n1);
    }

    public function test_pending_order_is_cancellable(): void
    {
        $order = $this->makeOrder(['status' => Order::STATUS_PENDING]);

        $this->assertTrue($order->isCancellable());
    }

    public function test_delivered_order_is_not_cancellable(): void
    {
        $order = $this->makeOrder(['status' => Order::STATUS_DELIVERED]);

        $this->assertFalse($order->isCancellable());
    }

    public function test_status_scope_filters_correctly(): void
    {
        $this->makeOrder(['status' => Order::STATUS_PENDING]);
        $this->makeOrder(['status' => Order::STATUS_DELIVERED]);

        $pending = Order::byStatus(Order::STATUS_PENDING)->get();

        $this->assertCount(1, $pending);
    }

    public function test_service_provider_is_registered(): void
    {
        $this->assertInstanceOf(
            \Ecommerce\Orders\OrdersServiceProvider::class,
            $this->app->getProvider(\Ecommerce\Orders\OrdersServiceProvider::class)
        );
    }

    public function test_config_is_loaded(): void
    {
        $this->assertNotNull(config('orders.currency'));
    }
}
