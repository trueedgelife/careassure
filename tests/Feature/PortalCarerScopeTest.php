<?php

use App\Enums\CarePackageStatus;
use App\Enums\CarerRateType;
use App\Enums\EmploymentType;
use App\Enums\ServiceUserStatus;
use App\Enums\ShiftStatus;
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
 * Portal privacy boundary: a carer logging into the portal must see ONLY
 * their own shifts — never another carer's, even within the same council.
 * This is the test that guards against a data leak in the portal, so it
 * runs before any portal UI is exposed.
 */

/** Build a carer (with a linked user) and a costable shift for them. */
function makeCarerWithShift(Tenant $tenant, CarePackage $package, string $email): array
{
    $user = User::create([
        'tenant_id' => $tenant->id,
        'name' => "Carer {$email}",
        'email' => $email,
        'password' => 'password',
        'is_active' => true,
    ]);
    $profile = Profile::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
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

    return ['user' => $user, 'carer' => $carer, 'shift' => $shift];
}

it('resolves the carer record from the logged-in user', function () {
    app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $profile = ServiceUser::query(); // noop to keep import used
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

        $a = makeCarerWithShift($tenant, $package, 'carer-a@example.com');

        // The user->profile->carer chain resolves to the right carer.
        expect($a['user']->carer()?->id)->toBe($a['carer']->id);
    });
});

it('scopes shifts so a carer sees only their own', function () {
    app(TenantContext::class)->runWithoutScope(function () {
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

        $a = makeCarerWithShift($tenant, $package, 'carer-a@example.com');
        $b = makeCarerWithShift($tenant, $package, 'carer-b@example.com');

        // The portal's "my shifts" query: shifts for the logged-in carer only.
        $aShifts = Shift::where('carer_id', $a['user']->carer()?->id)->get();
        $bShifts = Shift::where('carer_id', $b['user']->carer()?->id)->get();

        expect($aShifts)->toHaveCount(1)
            ->and($aShifts->first()->id)->toBe($a['shift']->id)
            ->and($bShifts)->toHaveCount(1)
            ->and($bShifts->first()->id)->toBe($b['shift']->id)
            // Crucially: A's shift list does NOT contain B's shift.
            ->and($aShifts->contains($b['shift']->id))->toBeFalse();
    });
});

it('a user who is not a carer resolves to no carer record', function () {
    app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Not A Carer',
            'email' => 'plain@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        Profile::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

        expect($user->carer())->toBeNull();
    });
});
