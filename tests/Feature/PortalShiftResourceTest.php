<?php

use App\Enums\CarePackageStatus;
use App\Enums\EmploymentType;
use App\Enums\ServiceUserStatus;
use App\Enums\ShiftStatus;
use App\Filament\Portal\Resources\Shifts\ShiftResource;
use App\Models\Carer;
use App\Models\CarePackage;
use App\Models\Profile;
use App\Models\ServiceUser;
use App\Models\Shift;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Proves the portal's enforcement point: ShiftResource::getEloquentQuery()
 * returns ONLY the authenticated carer's shifts. This is the actual code path
 * the portal uses, not just the underlying query — so it guards the real leak.
 */

function seedCarerWithShift(Tenant $tenant, CarePackage $package, string $email): array
{
    $user = User::create([
        'tenant_id' => $tenant->id,
        'name' => "Carer {$email}",
        'email' => $email,
        'password' => 'password',
        'is_active' => true,
    ]);
    $profile = Profile::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $carer = Carer::create([
        'tenant_id' => $tenant->id,
        'profile_id' => $profile->id,
        'employment_type' => EmploymentType::Employee,
        'is_active' => true,
    ]);
    $shift = Shift::create([
        'tenant_id' => $tenant->id,
        'care_package_id' => $package->id,
        'carer_id' => $carer->id,
        'scheduled_start_at' => '2025-02-01 09:00:00',
        'scheduled_end_at' => '2025-02-01 13:00:00',
        'status' => ShiftStatus::Completed,
    ]);

    return compact('user', 'carer', 'shift');
}

it('the portal Shift resource returns only the logged-in carer\'s shifts', function () {
    [$aUser, $aShift, $bShift] = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $su = ServiceUser::create([
            'tenant_id' => $tenant->id,
            'profile_id' => Profile::factory()->create(['tenant_id' => $tenant->id])->id,
            'status' => ServiceUserStatus::Active,
            'is_active' => true,
        ]);
        $package = CarePackage::create([
            'tenant_id' => $tenant->id,
            'service_user_id' => $su->id,
            'status' => CarePackageStatus::Active,
        ]);

        $a = seedCarerWithShift($tenant, $package, 'a@example.com');
        $b = seedCarerWithShift($tenant, $package, 'b@example.com');

        return [$a['user'], $a['shift'], $b['shift']];
    });

    // Authenticate as carer A and ask the resource for its query.
    $this->actingAs($aUser);
    app(TenantContext::class)->set($aUser->tenant_id);

    $ids = ShiftResource::getEloquentQuery()->pluck('id');

    expect($ids)->toContain($aShift->id)        // sees own shift
        ->and($ids)->not->toContain($bShift->id) // NOT the other carer's
        ->and($ids)->toHaveCount(1);
});

it('canAccess is false for a user who is not a carer', function () {
    $user = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $u = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Service User Only',
            'email' => 'plain@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        Profile::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $u->id]);
        return $u;
    });

    $this->actingAs($user);

    expect(ShiftResource::canAccess())->toBeFalse();
});
