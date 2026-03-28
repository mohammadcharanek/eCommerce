<?php

namespace Tests\Feature;

use App\Modules\Coupon\Models\Coupon;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\TenantSeeder::class);

        $this->admin = User::factory()->create(['tenant_id' => 1]);
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_create_coupon(): void
    {
        $response = $this->actingAs($this->admin)
            ->withHeaders(['X-Tenant-ID' => '1'])
            ->postJson('/api/v1/coupons', [
                'code'  => 'TESTCODE10',
                'type'  => 'percentage',
                'value' => 10,
            ]);

        $response->assertStatus(201)->assertJsonPath('data.code', 'TESTCODE10');
    }

    public function test_can_validate_valid_coupon(): void
    {
        Coupon::factory()->create([
            'code'       => 'SAVE20',
            'type'       => 'percentage',
            'value'      => 20,
            'tenant_id'  => 1,
            'is_active'  => true,
            'starts_at'  => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->postJson('/api/v1/coupons/validate', [
            'code'         => 'SAVE20',
            'order_amount' => 100.00,
        ]);

        $response->assertStatus(200)->assertJsonPath('data.discount', 20.0);
    }

    public function test_invalid_coupon_returns_error(): void
    {
        $response = $this->postJson('/api/v1/coupons/validate', [
            'code'         => 'INVALIDCODE',
            'order_amount' => 100.00,
        ]);

        $response->assertStatus(400);
    }

    public function test_expired_coupon_is_invalid(): void
    {
        Coupon::factory()->expired()->create([
            'code'      => 'EXPIRED',
            'tenant_id' => 1,
        ]);

        $response = $this->postJson('/api/v1/coupons/validate', [
            'code'         => 'EXPIRED',
            'order_amount' => 100.00,
        ]);

        $response->assertStatus(400);
    }
}
