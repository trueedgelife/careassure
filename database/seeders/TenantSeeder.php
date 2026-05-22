<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::firstOrCreate(
            ['slug' => 'demo-council'],
            [
                'name' => 'Demo Council',
                'contact_email' => 'admin@demo-council.local',
                'contact_phone' => '0117 000 0000',
                'is_active' => true,
                'settings' => [
                    'timezone' => 'Europe/London',
                    'currency' => 'GBP',
                ],
            ]
        );
    }
}
