<?php

namespace Database\Seeders;

use App\Modules\User\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::firstOrCreate(
            ['subdomain' => 'default'],
            [
                'name'      => 'Default Store',
                'domain'    => 'localhost',
                'subdomain' => 'default',
                'settings'  => [
                    'currency'         => 'USD',
                    'timezone'         => 'UTC',
                    'support_email'    => 'support@example.com',
                ],
                'is_active' => true,
            ]
        );

        $this->command->info('Default tenant seeded successfully.');
    }
}
