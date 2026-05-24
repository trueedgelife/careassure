<?php

use App\Enums\CarerRateType;
use App\Enums\EmploymentType;
use App\Filament\Portal\Resources\CarerCompliances\CarerComplianceResource;
use App\Filament\Portal\Resources\CarerRates\CarerRateResource;
use App\Models\Carer;
use App\Models\CarerCompliance;
use App\Models\CarerRate;
use App\Models\Profile;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Portal privacy: a carer sees only their OWN compliance record and pay rates,
 * never another carer's, even in the same council. Tests the real resource
 * getEloquentQuery() enforcement points.
 */

function makeCarerWithComplianceAndRate(Tenant $tenant, string $email): array
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
    $compliance = CarerCompliance::create([
        'tenant_id' => $tenant->id,
        'carer_id' => $carer->id,
        'dbs_certificate_number' => "DBS-{$email}",
    ]);
    $rate = CarerRate::create([
        'tenant_id' => $tenant->id,
        'carer_id' => $carer->id,
        'rate_type' => CarerRateType::Day,
        'hourly_rate' => 13.50,
        'effective_from' => '2024-01-01',
    ]);

    return compact('user', 'carer', 'compliance', 'rate');
}

it('a carer sees only their own compliance record in the portal', function () {
    [$aUser, $aComp, $bComp] = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $a = makeCarerWithComplianceAndRate($tenant, 'a@example.com');
        $b = makeCarerWithComplianceAndRate($tenant, 'b@example.com');
        return [$a['user'], $a['compliance'], $b['compliance']];
    });

    $this->actingAs($aUser);
    app(TenantContext::class)->set($aUser->tenant_id);

    $ids = CarerComplianceResource::getEloquentQuery()->pluck('id');

    expect($ids)->toContain($aComp->id)
        ->and($ids)->not->toContain($bComp->id)
        ->and($ids)->toHaveCount(1);
});

it('a carer sees only their own rates in the portal', function () {
    [$aUser, $aRate, $bRate] = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $a = makeCarerWithComplianceAndRate($tenant, 'a@example.com');
        $b = makeCarerWithComplianceAndRate($tenant, 'b@example.com');
        return [$a['user'], $a['rate'], $b['rate']];
    });

    $this->actingAs($aUser);
    app(TenantContext::class)->set($aUser->tenant_id);

    $ids = CarerRateResource::getEloquentQuery()->pluck('id');

    expect($ids)->toContain($aRate->id)
        ->and($ids)->not->toContain($bRate->id)
        ->and($ids)->toHaveCount(1);
});

it('compliance and rates resources are hidden from non-carers', function () {
    $user = app(TenantContext::class)->runWithoutScope(function () {
        $tenant = Tenant::factory()->create();
        $u = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Not A Carer',
            'email' => 'plain@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        Profile::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $u->id]);
        return $u;
    });

    $this->actingAs($user);

    expect(CarerComplianceResource::canAccess())->toBeFalse()
        ->and(CarerRateResource::canAccess())->toBeFalse();
});
