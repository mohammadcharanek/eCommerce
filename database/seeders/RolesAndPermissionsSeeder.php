<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Product permissions
            'view products', 'create products', 'edit products', 'delete products',
            // Category permissions
            'view categories', 'create categories', 'edit categories', 'delete categories',
            // Order permissions
            'view orders', 'create orders', 'edit orders', 'delete orders', 'manage orders',
            // Inventory permissions
            'view inventory', 'manage inventory',
            // Coupon permissions
            'view coupons', 'create coupons', 'edit coupons', 'delete coupons',
            // User permissions
            'view users', 'create users', 'edit users', 'delete users',
            // Payment permissions
            'process payments', 'refund payments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin role - all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Vendor role - product and order permissions
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $vendorRole->syncPermissions([
            'view products', 'create products', 'edit products',
            'view categories',
            'view orders', 'manage orders',
            'view inventory', 'manage inventory',
        ]);

        // Customer role - basic permissions
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->syncPermissions([
            'view products',
            'view categories',
            'create orders', 'view orders',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
