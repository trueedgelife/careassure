<?php

namespace Database\Seeders;

use App\Enums\LpaType;
use App\Enums\RelationshipType;
use App\Enums\ServiceUserStatus;
use App\Models\CapacityAssessment;
use App\Models\PersonRelationship;
use App\Models\ServiceUser;
use App\Models\Profile;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            RoleSeeder::class,
        ]);

        $demoTenant = Tenant::where('slug', 'demo-council')->firstOrFail();

        // ---- Platform super admin ----
        $super = User::firstOrCreate(
            ['tenant_id' => null, 'email' => 'super@trueedge.life'],
            [
                'name' => 'Platform Super Admin',
                'password' => Hash::make('password'),
                'is_super_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Super admin gets the super_admin role with no tenant context.
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);
        $super->syncRoles(['super_admin']);

        // ---- Demo council admin ----
        $admin = User::firstOrCreate(
            ['tenant_id' => $demoTenant->id, 'email' => 'admin@demo-council.local'],
            [
                'name' => 'Demo Council Admin',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        Profile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'tenant_id' => $demoTenant->id,
                'first_name' => 'Demo',
                'last_name' => 'Admin',
            ]
        );

        // Assign tenant_admin role within the demo tenant's context.
        app(PermissionRegistrar::class)->setPermissionsTeamId($demoTenant->id);
        $admin->syncRoles(['tenant_admin']);

        // ---- Demo council reviewer ----
        $reviewer = User::firstOrCreate(
            ['tenant_id' => $demoTenant->id, 'email' => 'reviewer@demo-council.local'],
            [
                'name' => 'Demo Council Reviewer',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        Profile::firstOrCreate(
            ['user_id' => $reviewer->id],
            [
                'tenant_id' => $demoTenant->id,
                'first_name' => 'Demo',
                'last_name' => 'Reviewer',
            ]
        );

        app(PermissionRegistrar::class)->setPermissionsTeamId($demoTenant->id);
        $reviewer->syncRoles(['council_reviewer']);

        // ---- Demo service user: Margaret Hughes ----
        $marProfile = Profile::firstOrCreate(
            ['tenant_id' => $demoTenant->id, 'first_name' => 'Margaret', 'last_name' => 'Hughes'],
            [
                'dob' => '1948-03-12',
                'address_line_1' => '14 Elm Court',
                'city' => 'Cardiff',
                'postcode' => 'CF10 1AA',
            ]
        );

        $serviceUser = ServiceUser::firstOrCreate(
            ['profile_id' => $marProfile->id],
            [
                'tenant_id' => $demoTenant->id,
                'nhs_number' => '943 476 5919',
                'council_reference' => 'CCC-2024-0142',
                'funding_start_date' => '2024-09-01',
                'status' => ServiceUserStatus::Active,
                'is_active' => true,
            ]
        );

        // Her daughter Sarah — emergency contact, holds Health & Welfare LPA
        $daughterProfile = Profile::firstOrCreate(
            ['tenant_id' => $demoTenant->id, 'first_name' => 'Sarah', 'last_name' => 'Hughes'],
            ['phone' => '07700 900123', 'city' => 'Cardiff']
        );

        PersonRelationship::firstOrCreate(
            ['service_user_id' => $serviceUser->id, 'related_profile_id' => $daughterProfile->id],
            [
                'tenant_id' => $demoTenant->id,
                'relationship_type' => RelationshipType::Child,
                'is_emergency_contact' => true,
                'has_lpa' => true,
                'lpa_type' => LpaType::HealthWelfare,
                'notes' => 'Primary contact; holds Health & Welfare LPA.',
            ]
        );

        CapacityAssessment::firstOrCreate(
            ['service_user_id' => $serviceUser->id, 'decision_domain' => 'financial'],
            [
                'tenant_id' => $demoTenant->id,
                'has_capacity' => false,
                'assessed_by' => 'J. Okafor (Social Worker)',
                'assessed_on' => '2024-08-20',
                'review_due' => '2025-08-20',
                'notes' => 'Lacks capacity for complex financial decisions; daughter manages via LPA.',
            ]
        );

    }
}
