<?php

namespace Tests\Feature;

use App\Modules\Product\Models\Category;
use App\Modules\Product\Models\Product;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\TenantSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->category = Category::factory()->create(['tenant_id' => 1]);
    }

    public function test_can_list_products(): void
    {
        Product::factory()->count(5)->create(['tenant_id' => 1, 'category_id' => $this->category->id]);

        $response = $this->getJson('/api/v1/products');
        $response->assertStatus(200)->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_can_create_product_as_admin(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/products', [
            'category_id' => $this->category->id,
            'name'        => 'Test Product',
            'price'       => 99.99,
            'sku'         => 'TEST-SKU-001',
        ]);

        $response->assertStatus(201)->assertJsonPath('data.name', 'Test Product');
    }

    public function test_cannot_create_product_as_guest(): void
    {
        $response = $this->postJson('/api/v1/products', [
            'category_id' => $this->category->id,
            'name'        => 'Test Product',
            'price'       => 99.99,
            'sku'         => 'TEST-SKU-002',
        ]);

        $response->assertStatus(401);
    }

    public function test_can_get_product(): void
    {
        $product = Product::factory()->create(['tenant_id' => 1, 'category_id' => $this->category->id]);

        $response = $this->getJson("/api/v1/products/{$product->id}");
        $response->assertStatus(200)->assertJsonPath('data.id', $product->id);
    }

    public function test_can_update_product_as_admin(): void
    {
        $product = Product::factory()->create(['tenant_id' => 1, 'category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)->putJson("/api/v1/products/{$product->id}", [
            'name'  => 'Updated Product Name',
            'price' => 149.99,
        ]);

        $response->assertStatus(200)->assertJsonPath('data.name', 'Updated Product Name');
    }

    public function test_can_delete_product_as_admin(): void
    {
        $product = Product::factory()->create(['tenant_id' => 1, 'category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)->deleteJson("/api/v1/products/{$product->id}");
        $response->assertStatus(200);
    }
}
