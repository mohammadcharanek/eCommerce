<?php

namespace Database\Factories;

use App\Modules\Product\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        return [
            'tenant_id'   => 1,
            'parent_id'   => null,
            'name'        => ucfirst($name),
            'slug'        => Str::slug($name) . '-' . Str::random(4),
            'description' => fake()->sentence(),
            'is_active'   => true,
            'sort_order'  => fake()->numberBetween(0, 100),
        ];
    }
}
