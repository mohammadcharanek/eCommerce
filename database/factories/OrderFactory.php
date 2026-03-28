<?php

namespace Database\Factories;

use App\Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 500);
        $tax = round($subtotal * 0.08, 2);
        $shipping = fake()->randomFloat(2, 0, 20);
        return [
            'tenant_id'        => 1,
            'user_id'          => \App\Modules\User\Models\User::factory(),
            'order_number'     => 'ORD-' . strtoupper(Str::random(4)) . '-' . now()->format('YmdHis'),
            'status'           => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered']),
            'subtotal'         => $subtotal,
            'discount'         => 0,
            'tax'              => $tax,
            'shipping'         => $shipping,
            'total'            => $subtotal + $tax + $shipping,
            'currency'         => 'USD',
            'shipping_address' => [
                'name'          => fake()->name(),
                'address_line_1'=> fake()->streetAddress(),
                'city'          => fake()->city(),
                'state'         => fake()->stateAbbr(),
                'postal_code'   => fake()->postcode(),
                'country'       => 'US',
            ],
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function delivered(): static
    {
        return $this->state(['status' => 'delivered']);
    }
}
