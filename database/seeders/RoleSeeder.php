<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * The fixed role catalogue. Tenants assign people to these roles —
     * they don't create their own. This keeps role semantics consistent
     * across councils, charities, and other tenants.
     */
    public const ROLES = [
        // Platform-level
        'super_admin' => 'Platform Super Admin',

        // Tenant staff
        'tenant_admin'      => 'Tenant Administrator',
        'care_coordinator'  => 'Care Coordinator',

        // Tenant — care delivery
        'carer'             => 'Carer / Personal Assistant',

        // Tenant — service user side
        'service_user'      => 'Service User',
        'delegate'          => 'Delegate (Family / Advocate)',

        // External oversight
        'council_reviewer'  => 'Council Reviewer (Read-only)',
    ];

    public function run(): void
    {
        // Roles live in the global catalogue (tenant_id = null). Assignments
        // are tenant-scoped via the model_has_roles.tenant_id column.
        // Temporarily clear team context so roles are created globally.
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

        foreach (self::ROLES as $slug => $label) {
            Role::firstOrCreate(
                ['name' => $slug, 'guard_name' => 'web'],
            );
        }
    }
}
