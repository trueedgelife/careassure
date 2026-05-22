<?php

namespace Database\Factories;

use App\Enums\CarerRateType;
use App\Models\Carer;
use App\Models\CarerRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarerRate>
 *
 * Defaults to a day hourly rate effective from a year ago with no end date.
 * Use ->state([...]) or the helpers below for night/sleep-in/flat rates.
 */
class CarerRateFactory extends Factory
{
    protected $model = CarerRate::class;

    public function definition(): array
    {
        return [
            // tenant_id is filled from the carer's tenant in configure();
            // if a carer_id is passed explicitly, set tenant_id too.
            'carer_id' => Carer::factory(),
            'rate_type' => CarerRateType::Day,
            'hourly_rate' => 13.50,
            'flat_rate' => null,
            'effective_from' => now()->subYear()->toDateString(),
            'effective_to' => null,
        ];
    }

    public function configure(): static
    {
        // Ensure tenant_id matches the carer the rate belongs to.
        return $this->afterMaking(function (CarerRate $rate) {
            if (! $rate->tenant_id && $rate->carer_id) {
                $rate->tenant_id = Carer::find($rate->carer_id)?->tenant_id;
            }
        })->afterCreating(function (CarerRate $rate) {
            if (! $rate->tenant_id && $rate->carer) {
                $rate->tenant_id = $rate->carer->tenant_id;
                $rate->save();
            }
        });
    }

    public function sleepIn(float $flat = 65.00): static
    {
        return $this->state(fn () => [
            'rate_type' => CarerRateType::SleepIn,
            'hourly_rate' => null,
            'flat_rate' => $flat,
        ]);
    }
}
