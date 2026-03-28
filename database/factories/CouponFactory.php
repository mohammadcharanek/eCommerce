<?php

namespace Database\Factories;

use App\Modules\Coupon\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'tenant_id'        => 1,
            'code'             => strtoupper(Str::random(8)),
            'type'             => fake()->randomElement(['percentage', 'fixed']),
            'value'            => fake()->randomFloat(2, 5, 50),
            'min_order_amount' => fake()->boolean(50) ? fake()->randomFloat(2, 10, 100) : null,
            'max_uses'         => fake()->boolean(50) ? fake()->numberBetween(10, 100) : null,
            'used_count'       => 0,
            'starts_at'        => now()->subDays(1),
            'expires_at'       => now()->addDays(30),
            'is_active'        => true,
        ];
    }

    public function percentage(): static
    {
        return $this->state(['type' => 'percentage', 'value' => fake()->numberBetween(5, 50)]);
    }

    public function fixed(): static
    {
        return $this->state(['type' => 'fixed', 'value' => fake()->numberBetween(5, 50)]);
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }
}
