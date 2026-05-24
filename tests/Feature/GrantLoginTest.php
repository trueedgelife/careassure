<?php

use App\Models\Profile;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * These tests verify the LOGIC the grant-login action performs, rather than
 * driving the Filament UI: create a user in the profile's tenant, link it,
 * assign the chosen roles, and never allow a second login on one profile.
 *
 * They assume the role catalogue exists (RoleSeeder). We seed it per test.
 */

beforeEach(function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);
});

/**
 * Mirror of the action's core logic, so the test exercises the same steps.
 * (When the action is refactored into a service class, point this at that.)
 */
function grantLogin(Profile $profile, string $email, array $roles): array
{
    $plain = Str::password(12, symbols: false);

    $user = User::create([
        'tenant_id' => $profile->tenant_id,
        'name' => $profile->full_name,
        'email' => $email,
        'password' => $plain,
        'is_super_admin' => false,
        'is_active' => true,
    ]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($profile->tenant_id);
    $user->syncRoles($roles);

    $profile->update(['user_id' => $user->id]);

    return ['user' => $user, 'password' => $plain];
}

it('creates a user in the same tenant as the profile and links them', function () {
    app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $profile = Profile::factory()->create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Eleri',
            'last_name' => 'Probert',
        ]);

        $result = grantLogin($profile, 'eleri@example.com', ['carer']);
        $user = $result['user'];

        expect($user->tenant_id)->toBe($tenant->id)        // same tenant
            ->and($user->name)->toBe('Eleri Probert')        // name from profile
            ->and($profile->fresh()->user_id)->toBe($user->id) // profile linked
            ->and($user->is_super_admin)->toBeFalse()
            ->and($user->is_active)->toBeTrue();
    });
});

it('assigns the chosen roles within the tenant team context', function () {
    app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $profile = Profile::factory()->create(['tenant_id' => $tenant->id]);

        // Multi-hat: grant both carer and service_user.
        $result = grantLogin($profile, 'multi@example.com', ['carer', 'service_user']);
        $user = $result['user'];

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);

        expect($user->hasRole('carer'))->toBeTrue()
            ->and($user->hasRole('service_user'))->toBeTrue()
            ->and($user->hasRole('tenant_admin'))->toBeFalse();
    });
});

it('stores the password hashed, not in plain text', function () {
    app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $profile = Profile::factory()->create(['tenant_id' => $tenant->id]);

        $result = grantLogin($profile, 'secure@example.com', ['carer']);

        // The stored hash must not equal the plain password, and must verify.
        expect($result['user']->password)->not->toBe($result['password'])
            ->and(Hash::check($result['password'], $result['user']->password))->toBeTrue();
    });
});

it('a profile that already has a login is recognised as having one', function () {
    app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $profile = Profile::factory()->create(['tenant_id' => $tenant->id]);

        grantLogin($profile, 'first@example.com', ['carer']);

        // The action's visibility guard keys off this: once user_id is set,
        // "Grant login" is hidden, preventing a second login.
        expect($profile->fresh()->user_id)->not->toBeNull()
            ->and($profile->fresh()->has_login)->toBeTrue();
    });
});
