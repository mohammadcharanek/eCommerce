<?php

namespace Ecommerce\Products\Tests\Unit;

use Ecommerce\Products\Models\Category;
use Ecommerce\Products\Models\Product;
use Ecommerce\Products\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $product = Product::create([
            'name'           => 'Test Product',
            'slug'           => 'test-product',
            'price'          => 29.99,
            'stock_quantity' => 100,
            'category_id'    => $category->id,
        ]);

        $this->assertDatabaseHas('products', ['slug' => 'test-product']);
        $this->assertEquals(29.99, (float) $product->price);
        $this->assertEquals(100, $product->stock_quantity);
    }

    public function test_product_is_in_stock(): void
    {
        $product = Product::factory()->make(['stock_quantity' => 10]);

        $this->assertTrue($product->isInStock());
    }

    public function test_product_is_out_of_stock(): void
    {
        $product = Product::factory()->make(['stock_quantity' => 0]);

        $this->assertFalse($product->isInStock());
    }

    public function test_decrement_stock_reduces_quantity(): void
    {
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product  = Product::create([
            'name'           => 'Widget',
            'slug'           => 'widget',
            'price'          => 10.00,
            'stock_quantity' => 50,
            'category_id'    => $category->id,
        ]);

        $product->decrementStock(10);

        $this->assertEquals(40, $product->fresh()->stock_quantity);
    }

    public function test_decrement_stock_throws_when_insufficient(): void
    {
        $category = Category::create(['name' => 'Test', 'slug' => 'test-2']);
        $product  = Product::create([
            'name'           => 'Gadget',
            'slug'           => 'gadget',
            'price'          => 5.00,
            'stock_quantity' => 2,
            'category_id'    => $category->id,
        ]);

        $this->expectException(\RuntimeException::class);
        $product->decrementStock(10);
    }

    public function test_discount_percentage_is_calculated(): void
    {
        $product = Product::factory()->make([
            'price'         => 80.00,
            'compare_price' => 100.00,
        ]);

        $this->assertEquals(20.0, $product->discount_percentage);
    }

    public function test_discount_percentage_is_null_when_no_compare_price(): void
    {
        $product = Product::factory()->make([
            'price'         => 80.00,
            'compare_price' => null,
        ]);

        $this->assertNull($product->discount_percentage);
    }

    public function test_active_scope_filters_correctly(): void
    {
        $category = Category::create(['name' => 'Scope Test', 'slug' => 'scope-test']);

        Product::create(['name' => 'Active Product',   'slug' => 'active-p',   'price' => 10, 'stock_quantity' => 5, 'is_active' => true,  'category_id' => $category->id]);
        Product::create(['name' => 'Inactive Product', 'slug' => 'inactive-p', 'price' => 10, 'stock_quantity' => 5, 'is_active' => false, 'category_id' => $category->id]);

        $active = Product::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals('Active Product', $active->first()->name);
    }

    public function test_service_provider_is_registered(): void
    {
        $this->assertInstanceOf(
            \Ecommerce\Products\ProductsServiceProvider::class,
            $this->app->getProvider(\Ecommerce\Products\ProductsServiceProvider::class)
        );
    }

    public function test_config_is_loaded(): void
    {
        $this->assertNotNull(config('products.per_page'));
    }
}
