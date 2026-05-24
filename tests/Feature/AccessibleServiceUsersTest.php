<?php

use App\Enums\RelationshipType;
use App\Enums\ServiceUserStatus;
use App\Models\PersonRelationship;
use App\Models\Profile;
use App\Models\ServiceUser;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * accessibleServiceUserIds() is the foundation of the service-user portal.
 * It must correctly resolve which service users a user may see — covering
 * plain service users, delegates (incl. for several people), people who are
 * BOTH, and users entitled to nothing. A wrong answer here either leaks
 * another family's care data or hides a delegate's own people. Test hard.
 */

/** Create a service user (with its own profile) in a tenant. */
function makeServiceUserRecord(Tenant $tenant): ServiceUser
{
    return ServiceUser::create([
        'tenant_id' => $tenant->id,
        'profile_id' => Profile::factory()->create(['tenant_id' => $tenant->id])->id,
        'status' => ServiceUserStatus::Active,
        'is_active' => true,
    ]);
}

/** Create a user with a profile in a tenant. */
function makeUserWithProfile(Tenant $tenant, string $email): array
{
    $user = User::create([
        'tenant_id' => $tenant->id,
        'name' => $email,
        'email' => $email,
        'password' => 'password',
        'is_active' => true,
    ]);
    $profile = Profile::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

    return [$user, $profile];
}

it('a plain service user sees only their own record', function () {
    [$id, $userId] = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        [$user, $profile] = makeUserWithProfile($tenant, 'su@example.com');

        $su = ServiceUser::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'status' => ServiceUserStatus::Active,
            'is_active' => true,
        ]);

        // An unrelated service user that must NOT appear.
        makeServiceUserRecord($tenant);

        return [$su->id, $user->id];
    });

    $user = User::find($userId);
    expect($user->accessibleServiceUserIds())->toBe([$id]);
});

it('a delegate sees the person they represent, not themselves', function () {
    [$repId, $userId] = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        [$user, $delegateProfile] = makeUserWithProfile($tenant, 'delegate@example.com');

        $represented = makeServiceUserRecord($tenant);

        PersonRelationship::create([
            'tenant_id' => $tenant->id,
            'service_user_id' => $represented->id,
            'related_profile_id' => $delegateProfile->id,
            'relationship_type' => RelationshipType::Child,
            'is_emergency_contact' => true,
            'has_lpa' => false,
        ]);

        return [$represented->id, $user->id];
    });

    $user = User::find($userId);
    expect($user->accessibleServiceUserIds())->toBe([$repId]);
});

it('a delegate for two people sees both', function () {
    [$rep1, $rep2, $userId] = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        [$user, $delegateProfile] = makeUserWithProfile($tenant, 'multi-delegate@example.com');

        $a = makeServiceUserRecord($tenant);
        $b = makeServiceUserRecord($tenant);

        foreach ([$a, $b] as $su) {
            PersonRelationship::create([
                'tenant_id' => $tenant->id,
                'service_user_id' => $su->id,
                'related_profile_id' => $delegateProfile->id,
                'relationship_type' => RelationshipType::Child,
                'is_emergency_contact' => false,
                'has_lpa' => false,
            ]);
        }

        return [$a->id, $b->id, $user->id];
    });

    $user = User::find($userId);
    $ids = $user->accessibleServiceUserIds();

    expect($ids)->toHaveCount(2)
        ->and($ids)->toContain($rep1)
        ->and($ids)->toContain($rep2);
});

it('a multi-hat user who is a service user AND a delegate sees both', function () {
    [$ownId, $repId, $userId] = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        [$user, $profile] = makeUserWithProfile($tenant, 'both@example.com');

        // Their own service-user record.
        $own = ServiceUser::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'status' => ServiceUserStatus::Active,
            'is_active' => true,
        ]);

        // And they're a delegate for someone else.
        $represented = makeServiceUserRecord($tenant);
        PersonRelationship::create([
            'tenant_id' => $tenant->id,
            'service_user_id' => $represented->id,
            'related_profile_id' => $profile->id,
            'relationship_type' => RelationshipType::Sibling,
            'is_emergency_contact' => false,
            'has_lpa' => false,
        ]);

        return [$own->id, $represented->id, $user->id];
    });

    $user = User::find($userId);
    $ids = $user->accessibleServiceUserIds();

    expect($ids)->toHaveCount(2)
        ->and($ids)->toContain($ownId)
        ->and($ids)->toContain($repId);
});

it('a user with no service-user or delegate role sees nothing', function () {
    $userId = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        [$user] = makeUserWithProfile($tenant, 'nobody@example.com');
        makeServiceUserRecord($tenant); // exists but unrelated
        return $user->id;
    });

    $user = User::find($userId);
    expect($user->accessibleServiceUserIds())->toBe([]);
});

it('a user with no profile at all sees nothing', function () {
    $userId = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'No Profile',
            'email' => 'noprofile@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        return $user->id;
    });

    $user = User::find($userId);
    expect($user->accessibleServiceUserIds())->toBe([]);
});
