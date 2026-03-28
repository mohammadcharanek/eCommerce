<?php

namespace Database\Seeders;

use App\Modules\Product\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'children' => ['Phones', 'Laptops', 'Tablets', 'Accessories']],
            ['name' => 'Clothing', 'children' => ['Men', 'Women', 'Kids', 'Sports']],
            ['name' => 'Home & Garden', 'children' => ['Furniture', 'Kitchen', 'Bedding', 'Outdoor']],
            ['name' => 'Books', 'children' => ['Fiction', 'Non-Fiction', 'Education', 'Comics']],
            ['name' => 'Sports', 'children' => ['Fitness', 'Outdoor', 'Team Sports', 'Water Sports']],
        ];

        foreach ($categories as $categoryData) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($categoryData['name'])],
                [
                    'tenant_id'   => 1,
                    'name'        => $categoryData['name'],
                    'slug'        => Str::slug($categoryData['name']),
                    'is_active'   => true,
                    'sort_order'  => 0,
                ]
            );

            foreach ($categoryData['children'] as $childName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($categoryData['name'] . '-' . $childName)],
                    [
                        'tenant_id'  => 1,
                        'parent_id'  => $parent->id,
                        'name'       => $childName,
                        'slug'       => Str::slug($categoryData['name'] . '-' . $childName),
                        'is_active'  => true,
                        'sort_order' => 0,
                    ]
                );
            }
        }

        $this->command->info('Categories seeded successfully.');
    }
}
