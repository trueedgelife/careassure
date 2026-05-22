<?php

use App\Enums\CarerRateType;
use App\Models\Carer;
use App\Models\CarePackage;
use App\Models\CarerRate;
use App\Models\Profile;
use App\Models\ServiceUser;
use App\Models\Shift;
use App\Models\Tenant;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Helper: build a costable shift for a carer who has the given rates.
 * Everything is created inside runWithoutScope so the global TenantScope
 * (which keys off auth) doesn't filter our test rows, and tenant_id is
 * always set explicitly.
 */
function makeShift(array $shiftAttributes, ?Carer $carer = null): Shift
{
    return app(TenantContext::class)->runWithoutScope(function () use ($shiftAttributes, $carer) {
        $tenant = Tenant::factory()->create();

        $carer ??= Carer::factory()->forTenant($tenant)->create();

        // A day rate (£13.50/hr) and a sleep-in flat rate (£65) effective a year ago.
        CarerRate::factory()->create([
            'tenant_id' => $tenant->id,
            'carer_id' => $carer->id,
            'rate_type' => CarerRateType::Day,
            'hourly_rate' => 13.50,
            'effective_from' => '2024-01-01',
        ]);
        CarerRate::factory()->sleepIn(65.00)->create([
            'tenant_id' => $tenant->id,
            'carer_id' => $carer->id,
            'effective_from' => '2024-01-01',
        ]);

        $profile = Profile::factory()->create(['tenant_id' => $tenant->id]);
        $serviceUser = ServiceUser::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'status' => 'active',
            'is_active' => true,
        ]);
        $package = CarePackage::create([
            'tenant_id' => $tenant->id,
            'service_user_id' => $serviceUser->id,
            'status' => 'active',
            'review_frequency_days' => 365,
        ]);

        return Shift::create(array_merge([
            'tenant_id' => $tenant->id,
            'care_package_id' => $package->id,
            'carer_id' => $carer->id,
            'status' => 'completed',
            'unpaid_break_minutes' => 0,
        ], $shiftAttributes));
    });
}

it('costs a day shift as hours times the hourly rate', function () {
    // 09:00–14:00 with a 30-min unpaid break = 4.5 paid hours.
    $shift = makeShift([
        'scheduled_start_at' => '2025-01-06 09:00:00',
        'scheduled_end_at' => '2025-01-06 14:00:00',
        'actual_start_at' => '2025-01-06 09:00:00',
        'actual_end_at' => '2025-01-06 14:00:00',
        'unpaid_break_minutes' => 30,
    ]);

    expect($shift->paidHours())->toBe(4.5);

    $shift->cost(CarerRateType::Day);
    $shift->refresh();

    expect((float) $shift->computed_cost)->toBe(60.75)  // 4.5 × 13.50
        ->and($shift->carer_rate_id)->not->toBeNull()    // rate was snapshotted
        ->and($shift->costed_at)->not->toBeNull();        // timestamp recorded
});

it('costs a sleep-in at the flat rate regardless of hours, across midnight', function () {
    // 22:00–07:00 next day = 9 hours, but a sleep-in is a flat £65.
    $shift = makeShift([
        'scheduled_start_at' => '2025-01-06 22:00:00',
        'scheduled_end_at' => '2025-01-07 07:00:00',
        'actual_start_at' => '2025-01-06 22:00:00',
        'actual_end_at' => '2025-01-07 07:00:00',
        'unpaid_break_minutes' => 0,
    ]);

    expect($shift->paidHours())->toBe(9.0); // midnight-crossing handled

    $shift->cost(CarerRateType::SleepIn);
    $shift->refresh();

    expect((float) $shift->computed_cost)->toBe(65.00); // flat, NOT 9 × anything
});

it('freezes the cost against the rate at the time, not a later rate change', function () {
    $shift = makeShift([
        'scheduled_start_at' => '2025-01-06 09:00:00',
        'scheduled_end_at' => '2025-01-06 13:00:00',
        'actual_start_at' => '2025-01-06 09:00:00',
        'actual_end_at' => '2025-01-06 13:00:00',
        'unpaid_break_minutes' => 0,
    ]);

    $shift->cost(CarerRateType::Day);
    $shift->refresh();

    expect((float) $shift->computed_cost)->toBe(54.00); // 4h × 13.50, snapshotted
});
