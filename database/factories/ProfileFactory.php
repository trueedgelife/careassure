<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 *
 * tenant_id defaults to a freshly-created tenant. Override with
 * ->state(['tenant_id' => $tenant->id]) to place it in a specific tenant.
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'city' => fake()->city(),
        ];
    }
}
