<?php

namespace Database\Factories;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        $price = fake()->randomFloat(2, 9.99, 999.99);
        return [
            'tenant_id'     => 1,
            'category_id'   => \App\Modules\Product\Models\Category::factory(),
            'name'          => ucfirst($name),
            'slug'          => Str::slug($name) . '-' . Str::random(6),
            'description'   => fake()->paragraphs(2, true),
            'price'         => $price,
            'compare_price' => fake()->boolean(40) ? $price * 1.2 : null,
            'sku'           => strtoupper(Str::random(10)),
            'is_active'     => true,
            'is_featured'   => fake()->boolean(20),
            'images'        => ['https://via.placeholder.com/800x600'],
            'meta'          => [],
        ];
    }

    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
