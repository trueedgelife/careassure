<?php

use App\Enums\CarePackageStatus;
use App\Enums\DeliveryModel;
use App\Enums\RelationshipType;
use App\Enums\ServiceUserStatus;
use App\Filament\Portal\Resources\CarePackages\CarePackageResource;
use App\Filament\Portal\Resources\DpAccounts\DpAccountResource;
use App\Models\CarePackage;
use App\Models\DpAccount;
use App\Models\PersonRelationship;
use App\Models\Profile;
use App\Models\ServiceUser;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Family-facing portal privacy. A service user / delegate sees only the
 * care package(s) and DP account(s) they're entitled to — and crucially,
 * council-managed packages expose NO account (the carer-wage privacy rule,
 * enforced by construction: those packages have no DP account at all).
 */

function makeServiceUserWithUser(Tenant $tenant, string $email, DeliveryModel $model): array
{
    $user = User::create([
        'tenant_id' => $tenant->id,
        'name' => $email,
        'email' => $email,
        'password' => 'password',
        'is_active' => true,
    ]);
    $profile = Profile::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $su = ServiceUser::create([
        'tenant_id' => $tenant->id,
        'profile_id' => $profile->id,
        'status' => ServiceUserStatus::Active,
        'is_active' => true,
    ]);
    $package = CarePackage::create([
        'tenant_id' => $tenant->id,
        'service_user_id' => $su->id,
        'delivery_model' => $model,
        'status' => CarePackageStatus::Active,
    ]);

    // DP account only when the model has one (mirrors real creation rule).
    $account = null;
    if ($model->hasDpAccount()) {
        $account = DpAccount::create([
            'tenant_id' => $tenant->id,
            'care_package_id' => $package->id,
            'opening_balance' => 0,
            'opened_on' => '2024-09-01',
        ]);
    }

    return compact('user', 'profile', 'su', 'package', 'account');
}

it('a service user sees only their own care package', function () {
    [$aUser, $aPkg, $bPkg] = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $a = makeServiceUserWithUser($tenant, 'a@example.com', DeliveryModel::DirectPayment);
        $b = makeServiceUserWithUser($tenant, 'b@example.com', DeliveryModel::DirectPayment);
        return [$a['user'], $a['package'], $b['package']];
    });

    $this->actingAs($aUser);
    app(TenantContext::class)->set($aUser->tenant_id);

    $ids = CarePackageResource::getEloquentQuery()->pluck('id');

    expect($ids)->toContain($aPkg->id)
        ->and($ids)->not->toContain($bPkg->id)
        ->and($ids)->toHaveCount(1);
});

it('a delegate sees the represented person\'s account, not their own absence of one', function () {
    [$delUser, $repAccountId] = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();

        // The represented person on a direct-payment package (has an account).
        $rep = makeServiceUserWithUser($tenant, 'rep@example.com', DeliveryModel::DirectPayment);

        // The delegate — a user with a profile but no service-user record.
        $delUser = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Delegate',
            'email' => 'delegate@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $delProfile = Profile::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $delUser->id]);

        PersonRelationship::create([
            'tenant_id' => $tenant->id,
            'service_user_id' => $rep['su']->id,
            'related_profile_id' => $delProfile->id,
            'relationship_type' => RelationshipType::Child,
            'is_emergency_contact' => true,
            'has_lpa' => true,
        ]);

        return [$delUser, $rep['account']->id];
    });

    $this->actingAs($delUser);
    app(TenantContext::class)->set($delUser->tenant_id);

    $accountIds = DpAccountResource::getEloquentQuery()->pluck('id');

    // The delegate sees the represented person's DP account.
    expect($accountIds)->toContain($repAccountId)
        ->and($accountIds)->toHaveCount(1);
});

it('a council-managed package exposes NO DP account to the family', function () {
    $cmUser = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        // Council-managed: by construction, no DP account is created.
        $cm = makeServiceUserWithUser($tenant, 'cm@example.com', DeliveryModel::CouncilManaged);
        return $cm['user'];
    });

    $this->actingAs($cmUser);
    app(TenantContext::class)->set($cmUser->tenant_id);

    // They can see their care package...
    expect(CarePackageResource::getEloquentQuery()->count())->toBe(1)
        // ...but there is no DP account to see (carer wages stay private).
        ->and(DpAccountResource::getEloquentQuery()->count())->toBe(0);
});

it('a mixed package DOES expose its DP account to the family', function () {
    [$mixUser, $accountId] = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $mix = makeServiceUserWithUser($tenant, 'mix@example.com', DeliveryModel::Mixed);
        return [$mix['user'], $mix['account']->id];
    });

    $this->actingAs($mixUser);
    app(TenantContext::class)->set($mixUser->tenant_id);

    $accountIds = DpAccountResource::getEloquentQuery()->pluck('id');

    expect($accountIds)->toContain($accountId)
        ->and($accountIds)->toHaveCount(1);
});
