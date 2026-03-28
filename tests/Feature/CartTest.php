<?php

namespace Tests\Feature;

use App\Modules\Inventory\Models\Inventory;
use App\Modules\Product\Models\Category;
use App\Modules\Product\Models\Product;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\TenantSeeder::class);

        $this->user = User::factory()->create(['tenant_id' => 1]);
        $this->user->assignRole('customer');

        $category = Category::factory()->create(['tenant_id' => 1]);
        $this->product = Product::factory()->create([
            'tenant_id'   => 1,
            'category_id' => $category->id,
            'price'       => 49.99,
        ]);

        Inventory::create([
            'product_id'         => $this->product->id,
            'quantity'           => 100,
            'reserved_quantity'  => 0,
            'low_stock_threshold'=> 5,
        ]);
    }

    public function test_can_get_cart(): void
    {
        $response = $this->actingAs($this->user)
            ->withHeaders(['X-Tenant-ID' => '1'])
            ->getJson('/api/v1/cart');

        $response->assertStatus(200)->assertJsonStructure(['success', 'data']);
    }

    public function test_can_add_item_to_cart(): void
    {
        $response = $this->actingAs($this->user)
            ->withHeaders(['X-Tenant-ID' => '1'])
            ->postJson('/api/v1/cart/items', [
                'product_id' => $this->product->id,
                'quantity'   => 2,
            ]);

        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_can_remove_item_from_cart(): void
    {
        // Add item first
        $this->actingAs($this->user)
            ->withHeaders(['X-Tenant-ID' => '1'])
            ->postJson('/api/v1/cart/items', [
                'product_id' => $this->product->id,
                'quantity'   => 1,
            ]);

        // Get cart to find item id
        $cartResponse = $this->actingAs($this->user)
            ->withHeaders(['X-Tenant-ID' => '1'])
            ->getJson('/api/v1/cart');

        $itemId = $cartResponse->json('data.items.0.id');

        $response = $this->actingAs($this->user)
            ->withHeaders(['X-Tenant-ID' => '1'])
            ->deleteJson("/api/v1/cart/items/{$itemId}");

        $response->assertStatus(200);
    }
}
