<?php

namespace Tests\Feature;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Order\Models\Order;
use App\Modules\Product\Models\Category;
use App\Modules\Product\Models\Product;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Product $product;
    protected Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\TenantSeeder::class);

        $this->user = User::factory()->create(['tenant_id' => 1]);
        $this->user->assignRole('customer');

        $this->admin = User::factory()->create(['tenant_id' => 1]);
        $this->admin->assignRole('admin');

        $category = Category::factory()->create(['tenant_id' => 1]);
        $this->product = Product::factory()->create([
            'tenant_id'   => 1,
            'category_id' => $category->id,
            'price'       => 99.99,
        ]);

        Inventory::create([
            'product_id'         => $this->product->id,
            'quantity'           => 50,
            'reserved_quantity'  => 0,
            'low_stock_threshold'=> 5,
        ]);

        $this->cart = Cart::create([
            'user_id'   => $this->user->id,
            'tenant_id' => 1,
            'expires_at'=> now()->addDays(7),
        ]);

        CartItem::create([
            'cart_id'    => $this->cart->id,
            'product_id' => $this->product->id,
            'quantity'   => 2,
            'price'      => 99.99,
        ]);
    }

    public function test_can_create_order(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/orders', [
            'cart_id'  => $this->cart->id,
            'shipping_address' => [
                'name'           => 'John Doe',
                'address_line_1' => '123 Main St',
                'city'           => 'New York',
                'state'          => 'NY',
                'postal_code'    => '10001',
                'country'        => 'US',
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'data' => ['orderNumber', 'status', 'total']]);
    }

    public function test_can_list_user_orders(): void
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id, 'tenant_id' => 1]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/orders');
        $response->assertStatus(200)->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_admin_can_update_order_status(): void
    {
        $order = Order::factory()->create([
            'user_id'   => $this->user->id,
            'tenant_id' => 1,
            'status'    => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->putJson("/api/v1/orders/{$order->id}/status", [
            'status' => 'processing',
        ]);

        $response->assertStatus(200)->assertJsonPath('data.status', 'processing');
    }

    public function test_user_can_cancel_order(): void
    {
        $order = Order::factory()->create([
            'user_id'   => $this->user->id,
            'tenant_id' => 1,
            'status'    => 'pending',
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/v1/orders/{$order->id}/cancel");
        $response->assertStatus(200)->assertJsonPath('data.status', 'cancelled');
    }
}
