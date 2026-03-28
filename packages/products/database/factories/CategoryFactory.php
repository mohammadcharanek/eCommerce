<?php

namespace Ecommerce\Products\Database\Factories;

use Ecommerce\Products\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'name'        => ucfirst($name),
            'slug'        => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(4),
            'description' => $this->faker->sentence(),
            'is_active'   => true,
        ];
    }
}
