<?php

namespace Database\Seeders;

use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            TenantSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'tenant_id' => 1,
                'name'      => 'Admin User',
                'email'     => 'admin@example.com',
                'password'  => Hash::make('password'),
                'phone'     => '+1234567890',
            ]
        );
        $admin->assignRole('admin');

        // Create vendor user
        $vendor = User::firstOrCreate(
            ['email' => 'vendor@example.com'],
            [
                'tenant_id' => 1,
                'name'      => 'Vendor User',
                'email'     => 'vendor@example.com',
                'password'  => Hash::make('password'),
            ]
        );
        $vendor->assignRole('vendor');

        // Create customer user
        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'tenant_id' => 1,
                'name'      => 'Customer User',
                'email'     => 'customer@example.com',
                'password'  => Hash::make('password'),
            ]
        );
        $customer->assignRole('customer');

        $this->command->info('Database seeded successfully with users: admin@example.com, vendor@example.com, customer@example.com (all password: "password")');
    }
}
