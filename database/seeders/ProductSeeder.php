<?php

namespace Database\Seeders;

use App\Modules\Inventory\Models\Inventory;
use App\Modules\Product\Models\Category;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronicsCategory = Category::where('slug', 'electronics')->first();
        $clothingCategory = Category::where('slug', 'clothing')->first();

        if (!$electronicsCategory || !$clothingCategory) {
            $this->command->warn('Categories not found. Run CategorySeeder first.');
            return;
        }

        $products = [
            [
                'category_id'   => $electronicsCategory->id,
                'name'          => 'Wireless Headphones Pro',
                'description'   => 'Premium wireless headphones with noise cancellation and 30-hour battery life.',
                'price'         => 199.99,
                'compare_price' => 249.99,
                'sku'           => 'WHP-PRO-001',
                'is_featured'   => true,
                'images'        => ['https://via.placeholder.com/800x600?text=Headphones'],
                'variants'      => [
                    ['sku' => 'WHP-PRO-BLK', 'price' => 199.99, 'attributes' => ['color' => 'Black']],
                    ['sku' => 'WHP-PRO-WHT', 'price' => 199.99, 'attributes' => ['color' => 'White']],
                ],
                'stock'         => 50,
            ],
            [
                'category_id'   => $electronicsCategory->id,
                'name'          => 'Smart Watch Ultra',
                'description'   => 'Advanced smartwatch with health monitoring, GPS, and 7-day battery.',
                'price'         => 399.99,
                'compare_price' => 499.99,
                'sku'           => 'SWU-001',
                'is_featured'   => true,
                'images'        => ['https://via.placeholder.com/800x600?text=SmartWatch'],
                'variants'      => [
                    ['sku' => 'SWU-42MM', 'price' => 399.99, 'attributes' => ['size' => '42mm']],
                    ['sku' => 'SWU-46MM', 'price' => 429.99, 'attributes' => ['size' => '46mm']],
                ],
                'stock'         => 30,
            ],
            [
                'category_id'   => $clothingCategory->id,
                'name'          => 'Premium Cotton T-Shirt',
                'description'   => '100% organic cotton t-shirt, comfortable and durable.',
                'price'         => 29.99,
                'compare_price' => null,
                'sku'           => 'TSH-COT-001',
                'is_featured'   => false,
                'images'        => ['https://via.placeholder.com/800x600?text=TShirt'],
                'variants'      => [
                    ['sku' => 'TSH-COT-S', 'price' => 29.99, 'attributes' => ['size' => 'S', 'color' => 'White']],
                    ['sku' => 'TSH-COT-M', 'price' => 29.99, 'attributes' => ['size' => 'M', 'color' => 'White']],
                    ['sku' => 'TSH-COT-L', 'price' => 29.99, 'attributes' => ['size' => 'L', 'color' => 'White']],
                ],
                'stock'         => 100,
            ],
        ];

        foreach ($products as $productData) {
            $variants = $productData['variants'];
            $stock = $productData['stock'];
            unset($productData['variants'], $productData['stock']);

            $productData['slug'] = Str::slug($productData['name']) . '-' . Str::random(4);
            $productData['tenant_id'] = 1;
            $productData['is_active'] = true;

            $product = Product::firstOrCreate(['sku' => $productData['sku']], $productData);

            Inventory::firstOrCreate(
                ['product_id' => $product->id, 'variant_id' => null],
                ['quantity' => $stock, 'reserved_quantity' => 0, 'low_stock_threshold' => 5]
            );

            foreach ($variants as $variantData) {
                $variant = ProductVariant::firstOrCreate(
                    ['sku' => $variantData['sku']],
                    array_merge($variantData, ['product_id' => $product->id])
                );

                Inventory::firstOrCreate(
                    ['product_id' => $product->id, 'variant_id' => $variant->id],
                    ['quantity' => (int)($stock / count($variants)), 'reserved_quantity' => 0, 'low_stock_threshold' => 3]
                );
            }
        }

        $this->command->info('Products seeded successfully.');
    }
}
