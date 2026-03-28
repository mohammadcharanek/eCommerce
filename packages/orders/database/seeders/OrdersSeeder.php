<?php

namespace Ecommerce\Orders\Database\Seeders;

use Ecommerce\Orders\Models\Order;
use Ecommerce\Products\Models\Product;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::active()->inStock()->take(10)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No active products found. Run ProductsSeeder first.');

            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $selectedProducts = $products->random(min(3, $products->count()));
            $subtotal         = 0;
            $items            = [];

            foreach ($selectedProducts as $product) {
                $qty       = rand(1, 3);
                $lineTotal = $product->price * $qty;
                $subtotal += $lineTotal;

                $items[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                    'quantity'     => $qty,
                    'unit_price'   => $product->price,
                    'subtotal'     => $lineTotal,
                ];
            }

            $taxAmount      = round($subtotal * 0.10, 2);
            $shippingAmount = 5.99;
            $total          = $subtotal + $taxAmount + $shippingAmount;

            $order = Order::create([
                'order_number'    => Order::generateOrderNumber(),
                'status'          => collect([Order::STATUS_PENDING, Order::STATUS_PROCESSING, Order::STATUS_DELIVERED])->random(),
                'subtotal'        => $subtotal,
                'tax_amount'      => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => 0,
                'total'           => $total,
                'currency'        => 'USD',
                'billing_name'    => "Sample Customer {$i}",
                'billing_email'   => "customer{$i}@example.com",
                'billing_phone'   => '+1-555-000-000' . $i,
                'billing_address' => "{$i} Main Street",
                'billing_city'    => 'Springfield',
                'billing_state'   => 'IL',
                'billing_zip'     => '62701',
                'billing_country' => 'US',
            ]);

            $order->items()->createMany($items);
        }
    }
}
