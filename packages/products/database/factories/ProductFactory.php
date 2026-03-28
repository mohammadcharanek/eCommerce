<?php

namespace Ecommerce\Products\Database\Factories;

use Ecommerce\Products\Models\Category;
use Ecommerce\Products\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'name'                => ucwords($name),
            'slug'                => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(4),
            'description'         => $this->faker->paragraph(),
            'short_description'   => $this->faker->sentence(),
            'price'               => $this->faker->randomFloat(2, 5, 500),
            'compare_price'       => $this->faker->optional()->randomFloat(2, 500, 1000),
            'cost_price'          => $this->faker->optional()->randomFloat(2, 1, 100),
            'stock_quantity'      => $this->faker->numberBetween(0, 200),
            'low_stock_threshold' => 5,
            'sku'                 => strtoupper($this->faker->unique()->bothify('SKU-####')),
            'category_id'         => Category::factory(),
            'is_active'           => true,
            'is_featured'         => false,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(['stock_quantity' => 0]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
