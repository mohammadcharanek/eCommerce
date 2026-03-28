<?php

namespace Ecommerce\Products\Database\Seeders;

use Ecommerce\Products\Models\Category;
use Ecommerce\Products\Models\Product;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics',    'slug' => 'electronics',    'description' => 'Electronic devices and accessories'],
            ['name' => 'Clothing',       'slug' => 'clothing',       'description' => 'Apparel and fashion items'],
            ['name' => 'Home & Garden',  'slug' => 'home-garden',    'description' => 'Products for home and garden'],
            ['name' => 'Sports',         'slug' => 'sports',         'description' => 'Sports and outdoor equipment'],
            ['name' => 'Books',          'slug' => 'books',          'description' => 'Books and educational material'],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            // Seed 5 sample products per category
            for ($i = 1; $i <= 5; $i++) {
                $name = "{$category->name} Product {$i}";
                $slug = $categoryData['slug'] . "-product-{$i}";

                Product::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name'                => $name,
                        'slug'                => $slug,
                        'description'         => "This is a sample description for {$name}.",
                        'short_description'   => "Sample short description for {$name}.",
                        'price'               => rand(999, 9999) / 100,
                        'compare_price'       => rand(10000, 19999) / 100,
                        'stock_quantity'      => rand(10, 200),
                        'low_stock_threshold' => 5,
                        'sku'                 => strtoupper($categoryData['slug']) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                        'category_id'         => $category->id,
                        'is_active'           => true,
                        'is_featured'         => $i === 1,
                    ]
                );
            }
        }
    }
}
