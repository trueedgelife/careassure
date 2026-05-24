<?php

use App\Enums\EmploymentType;
use App\Enums\RelationshipType;
use App\Enums\ServiceUserStatus;
use App\Models\Carer;
use App\Models\PersonRelationship;
use App\Models\Profile;
use App\Models\ServiceUser;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * The core promise of the data model: a person is a Profile, and roles
 * ("hats") attach to that one profile. The same human can simultaneously
 * be a carer, a service user, have a login, and be someone else's contact.
 */

it('lets one profile be both a carer and a service user at once', function () {
    app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();

        $profile = Profile::factory()->create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Eleri',
            'last_name' => 'Probert',
        ]);

        Carer::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'employment_type' => EmploymentType::Employee,
            'is_primary_carer' => false,
            'is_active' => true,
        ]);

        ServiceUser::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'status' => ServiceUserStatus::Active,
            'is_active' => true,
        ]);

        $profile->refresh();

        // Both hats resolve off the single profile.
        expect($profile->carer)->not->toBeNull()
            ->and($profile->serviceUser)->not->toBeNull()
            ->and($profile->is_carer)->toBeTrue()
            ->and($profile->is_service_user)->toBeTrue();
    });
});

it('reports has_login correctly based on a linked user', function () {
    app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();

        // A person with no login — the common case.
        $noLogin = Profile::factory()->create(['tenant_id' => $tenant->id]);
        expect($noLogin->has_login)->toBeFalse();

        // A person with a login.
        $user = User::create([
            'tenant_id' => $tenant->id,
            'email' => 'eleri@example.com',
            'name' => 'Eleri Probert',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $withLogin = Profile::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
        ]);

        expect($withLogin->has_login)->toBeTrue()
            ->and($withLogin->user->email)->toBe('eleri@example.com');
    });
});

it('lets a profile be the emergency contact for another service user', function () {
    app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();

        // The person who will be a contact.
        $contactProfile = Profile::factory()->create(['tenant_id' => $tenant->id]);

        // A different service user they are the contact for.
        $otherProfile = Profile::factory()->create(['tenant_id' => $tenant->id]);
        $otherServiceUser = ServiceUser::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $otherProfile->id,
            'status' => ServiceUserStatus::Active,
            'is_active' => true,
        ]);

        PersonRelationship::create([
            'tenant_id' => $tenant->id,
            'service_user_id' => $otherServiceUser->id,
            'related_profile_id' => $contactProfile->id,
            'relationship_type' => RelationshipType::NextOfKin,
            'is_emergency_contact' => true,
            'has_lpa' => false,
        ]);

        $contactProfile->refresh();

        // The reverse relation surfaces "relationships where I am the contact".
        expect($contactProfile->relationshipsAsContact)->toHaveCount(1)
            ->and($contactProfile->relationshipsAsContact->first()->is_emergency_contact)->toBeTrue()
            ->and($contactProfile->relationshipsAsContact->first()->service_user_id)->toBe($otherServiceUser->id);
    });
});

it('supports the full stack: carer + service user + login + contact on one profile', function () {
    app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();

        $user = User::create([
            'tenant_id' => $tenant->id,
            'email' => 'multihat@example.com',
            'name' => 'Eleri Probert',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $profile = Profile::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
        ]);

        Carer::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'employment_type' => EmploymentType::Employee,
            'is_active' => true,
        ]);
        ServiceUser::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'status' => ServiceUserStatus::Active,
            'is_active' => true,
        ]);

        $otherProfile = Profile::factory()->create(['tenant_id' => $tenant->id]);
        $otherSu = ServiceUser::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $otherProfile->id,
            'status' => ServiceUserStatus::Active,
            'is_active' => true,
        ]);
        PersonRelationship::create([
            'tenant_id' => $tenant->id,
            'service_user_id' => $otherSu->id,
            'related_profile_id' => $profile->id,
            'relationship_type' => RelationshipType::NextOfKin,
            'is_emergency_contact' => true,
            'has_lpa' => false,
        ]);

        $profile->refresh();

        expect($profile->is_carer)->toBeTrue()
            ->and($profile->is_service_user)->toBeTrue()
            ->and($profile->has_login)->toBeTrue()
            ->and($profile->relationshipsAsContact)->toHaveCount(1);
    });
});
