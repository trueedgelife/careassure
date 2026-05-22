<?php

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Models\Carer;
use App\Models\Profile;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Carer>
 *
 * Tenant-scoped: sets tenant_id explicitly so tests don't rely on auth/context.
 * If no tenant is given, creates one. Profile is created in the same tenant.
 */
class CarerFactory extends Factory
{
    protected $model = Carer::class;

    public function definition(): array
    {
        $tenant = Tenant::factory()->create();

        return [
            'tenant_id' => $tenant->id,
            'profile_id' => Profile::factory()->state(['tenant_id' => $tenant->id]),
            'employment_type' => EmploymentType::Employee,
            'start_date' => now()->subYear(),
            'is_primary_carer' => true,
            'is_active' => true,
        ];
    }

    /**
     * Place this carer in an existing tenant (keeps profile in the same tenant).
     */
    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn () => [
            'tenant_id' => $tenant->id,
            'profile_id' => Profile::factory()->state(['tenant_id' => $tenant->id]),
        ]);
    }
}
