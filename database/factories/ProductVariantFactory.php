<?php

namespace Database\Factories;

use App\Modules\Product\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => \App\Modules\Product\Models\Product::factory(),
            'sku'        => strtoupper(Str::random(12)),
            'price'      => fake()->randomFloat(2, 9.99, 499.99),
            'attributes' => [
                'color' => fake()->colorName(),
                'size'  => fake()->randomElement(['XS', 'S', 'M', 'L', 'XL']),
            ],
        ];
    }
}
