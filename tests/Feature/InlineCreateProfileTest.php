<?php

use App\Models\Carer;
use App\Models\Profile;
use App\Models\Tenant;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * The inline "create person" option on the Carer/ServiceUser forms relies on
 * a new Profile getting its tenant_id automatically (via the BelongsToTenant
 * trait, from the active tenant context). These tests confirm that behaviour
 * so the inline-create doesn't silently produce tenant-less profiles.
 */

it('auto-sets tenant_id on a profile created within a tenant context', function () {
    $tenant = Tenant::factory()->create();

    // Simulate the request being scoped to this tenant (as it is when an
    // authenticated admin uses the form).
    app(TenantContext::class)->set($tenant->id);

    // Create WITHOUT explicitly passing tenant_id — the trait should fill it.
    $profile = Profile::create([
        'first_name' => 'Created',
        'last_name' => 'Inline',
    ]);

    expect($profile->tenant_id)->toBe($tenant->id);
});

it('a profile created inline can immediately be made a carer', function () {
    $tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($tenant->id);

    // This mirrors what createOptionForm + the parent form do in sequence:
    // create the profile, then attach the carer record to it.
    $profile = Profile::create([
        'first_name' => 'New',
        'last_name' => 'Carer',
    ]);

    $carer = Carer::create([
        'profile_id' => $profile->id,
        'employment_type' => \App\Enums\EmploymentType::Employee,
        'is_active' => true,
    ]);

    expect($carer->tenant_id)->toBe($tenant->id)         // carer also auto-tenanted
        ->and($carer->profile->id)->toBe($profile->id)    // linked correctly
        ->and($profile->tenant_id)->toBe($tenant->id);
});
