<?php

namespace Database\Seeders;

use App\Enums\CarePackageStatus;
use App\Enums\CarerRateType;
use App\Enums\EmploymentType;
use App\Enums\FunderType;
use App\Enums\IncidentSeverity;
use App\Enums\IncidentStatus;
use App\Enums\RelationshipType;
use App\Enums\ServiceUserStatus;
use App\Enums\ShiftStatus;
use App\Enums\TransactionDirection;
use App\Enums\TransactionSource;
use App\Models\CarePackage;
use App\Models\Carer;
use App\Models\CarerCompliance;
use App\Models\CarerRate;
use App\Models\DpAccount;
use App\Models\DpTransaction;
use App\Models\FundingSource;
use App\Models\Incident;
use App\Models\PersonRelationship;
use App\Models\Profile;
use App\Models\ServiceUser;
use App\Models\Shift;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds five faker councils with deliberately uneven domain data.
 *
 * Login convention:  {role}{n}@{council-slug}.example.com   (password: "password")
 *   e.g. admin1@northgate.example.com, carer2@northgate.example.com
 *
 * Run with:  php artisan db:seed --class=DemoDataSeeder
 * (Kept separate from DatabaseSeeder so the core seed stays lean and the
 *  existing Demo Council / Margaret / Tom data is untouched.)
 *
 * The councils vary in size AND completeness on purpose — partial records
 * (no package, no funding, uncosted shifts, no compliance, missing profile
 * fields, inactive entities) are exactly what flush out null-handling bugs.
 */
class DemoDataSeeder extends Seeder
{
    protected PermissionRegistrar $permissions;
    protected TenantContext $tenantContext;

    /**
     * Profiles for whom we created a LOGIN user, used to wire relationships
     * and delegates within a council. Keyed by council index.
     */
    protected array $councilServiceUsers = [];

    public function run(): void
    {
        $this->permissions = app(PermissionRegistrar::class);
        $this->tenantContext = app(TenantContext::class);

        // Make sure the role catalogue exists (idempotent with RoleSeeder).
        $this->call(RoleSeeder::class);

        // Platform super admin on the example.com domain (in addition to any
        // existing super@trueedge.life from the core seeder).
        $this->permissions->setPermissionsTeamId(null);
        $super = $this->makeUser(null, 'super@example.com', 'Platform Super Admin', true);
        $super->syncRoles(['super_admin']);

        $councils = [
            ['name' => 'East Frederique Council', 'slug' => 'east-frederique', 'service_users' => 20, 'admins' => 2, 'coordinators' => 1, 'carers' => 4, 'reviewers' => 1, 'profile' => 'large',   'active' => true],
            ['name' => 'Bartolettitown Council',  'slug' => 'bartolettitown',  'service_users' => 6,  'admins' => 2, 'coordinators' => 1, 'carers' => 3, 'reviewers' => 1, 'profile' => 'healthy', 'active' => true],
            ['name' => 'West Sarai Council',       'slug' => 'west-sarai',      'service_users' => 8,  'admins' => 1, 'coordinators' => 1, 'carers' => 2, 'reviewers' => 1, 'profile' => 'messy',   'active' => true],
            ['name' => 'Rauberg Council',          'slug' => 'rauberg',         'service_users' => 3,  'admins' => 1, 'coordinators' => 0, 'carers' => 1, 'reviewers' => 0, 'profile' => 'small',   'active' => true],
            ['name' => 'Lake Jalen Council',       'slug' => 'lake-jalen',      'service_users' => 1,  'admins' => 1, 'coordinators' => 0, 'carers' => 0, 'reviewers' => 0, 'profile' => 'empty',   'active' => false],
        ];

        foreach ($councils as $i => $bp) {
            $this->seedCouncil($i + 1, $bp);
        }

        $this->seedMultiHatPerson();

        $this->command?->info('DemoDataSeeder complete. Logins: {role}{n}@{slug}.example.com / password');
    }

    protected function seedCouncil(int $index, array $bp): void
    {
        $name = $bp['name'];
        $slug = $bp['slug'];

        $tenant = Tenant::create([
            'name' => $name,
            'slug' => $slug,
            'contact_email' => "info@{$slug}.example.com",
            'contact_phone' => fake()->phoneNumber(),
            'is_active' => $bp['active'],
            'settings' => ['timezone' => 'Europe/London', 'currency' => 'GBP'],
        ]);

        $this->permissions->setPermissionsTeamId($tenant->id);

        // ---- Staff logins ----
        for ($n = 1; $n <= $bp['admins']; $n++) {
            $u = $this->makeUser($tenant->id, "admin{$n}@{$slug}.example.com", fake()->name(), false);
            $this->makeProfileFor($u, $tenant, $bp['profile']);
            $u->syncRoles(['tenant_admin']);
        }
        for ($n = 1; $n <= $bp['coordinators']; $n++) {
            $u = $this->makeUser($tenant->id, "coordinator{$n}@{$slug}.example.com", fake()->name(), false);
            $this->makeProfileFor($u, $tenant, $bp['profile']);
            $u->syncRoles(['care_coordinator']);
        }
        for ($n = 1; $n <= $bp['reviewers']; $n++) {
            $u = $this->makeUser($tenant->id, "reviewer{$n}@{$slug}.example.com", fake()->name(), false);
            $this->makeProfileFor($u, $tenant, $bp['profile']);
            $u->syncRoles(['council_reviewer']);
        }

        // ---- Carers (with login + carer record + rates + compliance) ----
        $carers = [];
        for ($n = 1; $n <= $bp['carers']; $n++) {
            $carers[] = $this->makeCarer($tenant, $slug, $n, $bp['profile'], $index);
        }

        // ---- Service users + their domain data ----
        for ($n = 1; $n <= $bp['service_users']; $n++) {
            $this->makeServiceUser($tenant, $slug, $n, $bp['profile'], $carers, $index);
        }
    }

    /**
     * Creates one deliberate "multi-hat" person to prove the data model:
     * the same Profile is a carer, a service user, has a login with TWO
     * roles, and is the emergency-contact delegate for another service user.
     *
     * Lives in Bartolettitown. Login: multihat@bartolettitown.example.com
     */
    protected function seedMultiHatPerson(): void
    {
        $tenant = Tenant::where('slug', 'bartolettitown')->first();
        if (! $tenant) {
            return;
        }

        $this->permissions->setPermissionsTeamId($tenant->id);

        // The person + their login.
        $user = $this->makeUser($tenant->id, "multihat@bartolettitown.example.com", 'Eleri Probert', false);
        $profile = Profile::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'first_name' => 'Eleri',
            'last_name' => 'Probert',
            'phone' => fake()->phoneNumber(),
            'dob' => '1979-04-12',
            'city' => fake()->city(),
            'postcode' => fake()->postcode(),
        ]);

        // Hat 1 + 2: a login with BOTH carer and service_user roles.
        $user->syncRoles(['carer', 'service_user']);

        // Hat 3: she is a carer (employment record + a rate).
        $carer = Carer::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'employment_type' => EmploymentType::Employee,
            'start_date' => '2023-06-01',
            'is_primary_carer' => false,
            'is_active' => true,
        ]);
        CarerRate::create([
            'tenant_id' => $tenant->id,
            'carer_id' => $carer->id,
            'rate_type' => CarerRateType::Day,
            'hourly_rate' => 14.00,
            'effective_from' => '2024-01-01',
        ]);

        // Hat 4: she is ALSO a service user in her own right.
        $ownServiceUser = ServiceUser::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'council_reference' => 'BAR-MH01',
            'status' => ServiceUserStatus::Active,
            'is_active' => true,
        ]);

        // Hat 5: she is the emergency-contact delegate for a DIFFERENT service
        // user (the first one seeded in Bartolettitown), via a relationship.
        $otherServiceUser = ServiceUser::where('tenant_id', $tenant->id)
            ->where('id', '!=', $ownServiceUser->id)
            ->first();

        if ($otherServiceUser) {
            PersonRelationship::create([
                'tenant_id' => $tenant->id,
                'service_user_id' => $otherServiceUser->id,
                'related_profile_id' => $profile->id,
                'relationship_type' => RelationshipType::NextOfKin,
                'is_emergency_contact' => true,
                'has_lpa' => false,
            ]);
        }
    }

    /* ---------------------------------------------------------------- */
    /* Builders                                                          */
    /* ---------------------------------------------------------------- */

    protected function makeUser(?int $tenantId, string $email, string $name, bool $isSuper): User
    {
        return User::create([
            'tenant_id' => $tenantId,
            'email' => $email,
            'name' => $name, // NEVER null — null name crashes Filament's user menu
            'password' => Hash::make('password'),
            'is_super_admin' => $isSuper,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create a profile for a login user. Completeness varies by council
     * "profile" style so some records are deliberately sparse.
     */
    protected function makeProfileFor(User $user, Tenant $tenant, string $style): Profile
    {
        [$first, $last] = $this->splitName($user->name);

        $attrs = [
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'first_name' => $first,
            'last_name' => $last,
        ];

        // "messy" and "empty" councils leave optional fields blank on purpose.
        if (! in_array($style, ['messy', 'empty'], true)) {
            $attrs += [
                'phone' => fake()->phoneNumber(),
                'dob' => fake()->dateTimeBetween('-90 years', '-20 years')->format('Y-m-d'),
                'address_line_1' => fake()->streetAddress(),
                'city' => fake()->city(),
                'postcode' => fake()->postcode(),
            ];
        }

        return Profile::create($attrs);
    }

    protected function makeCarer(Tenant $tenant, string $slug, int $n, string $style, int $councilIndex): Carer
    {
        $user = $this->makeUser($tenant->id, "carer{$n}@{$slug}.example.com", fake()->name(), false);
        $profile = $this->makeProfileFor($user, $tenant, $style);
        $user->syncRoles(['carer']);

        // First carer in a council can be inactive (tests non-active filtering).
        $isActive = ! ($n === 1 && $style === 'messy');

        $carer = Carer::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'employment_type' => fake()->randomElement(EmploymentType::cases()),
            'start_date' => fake()->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d'),
            'is_primary_carer' => $n === 1,
            'is_active' => $isActive,
        ]);

        // PARTIAL: some carers get no rates (cannot be costed) and no compliance.
        $giveRates = ! ($style === 'messy' && $n === 2);
        $giveCompliance = ! ($style === 'small') && $n !== 1;

        if ($giveRates) {
            CarerRate::create([
                'tenant_id' => $tenant->id,
                'carer_id' => $carer->id,
                'rate_type' => CarerRateType::Day,
                'hourly_rate' => fake()->randomElement([12.50, 13.50, 14.00, 15.25]),
                'effective_from' => '2024-01-01',
            ]);
            CarerRate::create([
                'tenant_id' => $tenant->id,
                'carer_id' => $carer->id,
                'rate_type' => CarerRateType::SleepIn,
                'flat_rate' => fake()->randomElement([60, 65, 70]),
                'effective_from' => '2024-01-01',
            ]);
        }

        if ($giveCompliance) {
            CarerCompliance::create([
                'tenant_id' => $tenant->id,
                'carer_id' => $carer->id,
                'dbs_certificate_number' => (string) fake()->numerify('############'),
                'dbs_issued_on' => fake()->dateTimeBetween('-2 years', '-1 month')->format('Y-m-d'),
                'dbs_on_update_service' => fake()->boolean(70),
                'right_to_work_verified_on' => fake()->dateTimeBetween('-2 years', '-1 month')->format('Y-m-d'),
                // PARTIAL: some training-expiry dates left null.
                'safeguarding_expires_on' => fake()->boolean(80) ? fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d') : null,
                'first_aid_expires_on' => fake()->boolean(60) ? fake()->dateTimeBetween('-3 months', '+2 years')->format('Y-m-d') : null,
            ]);
        }

        return $carer;
    }

    protected function makeServiceUser(Tenant $tenant, string $slug, int $n, string $style, array $carers, int $councilIndex): void
    {
        $name = fake()->name();
        [$first, $last] = $this->splitName($name);

        $profileAttrs = [
            'tenant_id' => $tenant->id,
            'first_name' => $first,
            'last_name' => $last,
        ];
        if (! in_array($style, ['messy', 'empty'], true)) {
            $profileAttrs += [
                'dob' => fake()->dateTimeBetween('-95 years', '-50 years')->format('Y-m-d'),
                'address_line_1' => fake()->streetAddress(),
                'city' => fake()->city(),
                'postcode' => fake()->postcode(),
            ];
        }
        $profile = Profile::create($profileAttrs);

        // Status varies: most active, some assessment/closed/deceased.
        $status = match (true) {
            $n === 1 && $style === 'messy' => ServiceUserStatus::Assessment, // no package yet
            $n === 2 && $style === 'large' => ServiceUserStatus::Closed,
            $n === 3 && $style === 'large' => ServiceUserStatus::Deceased,
            default => ServiceUserStatus::Active,
        };

        $serviceUser = ServiceUser::create([
            'tenant_id' => $tenant->id,
            'profile_id' => $profile->id,
            'nhs_number' => fake()->boolean(70) ? fake()->numerify('### ### ####') : null,
            'council_reference' => strtoupper(substr($slug, 0, 3)) . '-' . fake()->numerify('####'),
            'funding_start_date' => fake()->boolean(80) ? fake()->dateTimeBetween('-2 years', '-1 month')->format('Y-m-d') : null,
            'status' => $status,
            'is_active' => $status === ServiceUserStatus::Active,
        ]);

        // Some service users get a login + delegate; most don't.
        if ($n <= 2 && $style !== 'empty') {
            $suUser = $this->makeUser($tenant->id, "serviceuser{$n}@{$slug}.example.com", $name, false);
            $profile->update(['user_id' => $suUser->id]);
            $suUser->syncRoles(['service_user']);

            // A delegate (family member) with a login + relationship.
            if ($n === 1) {
                $delUser = $this->makeUser($tenant->id, "delegate1@{$slug}.example.com", fake()->name(), false);
                $delProfile = $this->makeProfileFor($delUser, $tenant, $style);
                $delUser->syncRoles(['delegate']);

                PersonRelationship::create([
                    'tenant_id' => $tenant->id,
                    'service_user_id' => $serviceUser->id,
                    'related_profile_id' => $delProfile->id,
                    'relationship_type' => RelationshipType::Child,
                    'is_emergency_contact' => true,
                    'has_lpa' => fake()->boolean(50),
                    'lpa_type' => null, // PARTIAL: has_lpa true but type sometimes null
                ]);
            }
        }

        // PARTIAL: assessment-status users get NO care package at all.
        if ($status === ServiceUserStatus::Assessment) {
            return;
        }

        $this->makePackage($tenant, $serviceUser, $n, $style, $carers);
    }

    protected function makePackage(Tenant $tenant, ServiceUser $serviceUser, int $n, string $style, array $carers): void
    {
        $packageStatus = match ($serviceUser->status) {
            ServiceUserStatus::Closed, ServiceUserStatus::Deceased => CarePackageStatus::Closed,
            default => CarePackageStatus::Active,
        };

        $package = CarePackage::create([
            'tenant_id' => $tenant->id,
            'service_user_id' => $serviceUser->id,
            'weekly_funded_hours' => fake()->boolean(85) ? fake()->randomElement([14, 21, 28, 35]) : null,
            'annual_budget' => fake()->boolean(80) ? fake()->randomElement([12000, 14742, 18500, 22000]) : null,
            'review_frequency_days' => 365,
            'status' => $packageStatus,
        ]);

        // PARTIAL: ~1 in 4 packages get NO funding source (drafted, unfunded).
        if (fake()->boolean(75)) {
            FundingSource::create([
                'tenant_id' => $tenant->id,
                'care_package_id' => $package->id,
                'funder_type' => FunderType::LocalAuthority,
                'funder_name' => $tenant->name,
                'weekly_amount' => fake()->randomElement([150, 189, 240]),
                'effective_from' => '2024-09-01',
            ]);
            // Sometimes a second (NHS) funder.
            if (fake()->boolean(40)) {
                FundingSource::create([
                    'tenant_id' => $tenant->id,
                    'care_package_id' => $package->id,
                    'funder_type' => FunderType::NhsChc,
                    'funder_name' => 'NHS ICB',
                    'weekly_amount' => fake()->randomElement([80, 94.50, 120]),
                    'effective_from' => '2024-09-01',
                ]);
            }
        }

        // Shifts — only if there are carers to assign. Some left UNCOSTED.
        if (! empty($carers) && $packageStatus === CarePackageStatus::Active) {
            $shiftCount = fake()->numberBetween(0, 4); // some packages get 0 shifts
            for ($s = 0; $s < $shiftCount; $s++) {
                $carer = fake()->randomElement($carers);
                $day = fake()->dateTimeBetween('-3 weeks', 'now');
                $start = (clone $day)->setTime(9, 0);
                $end = (clone $day)->setTime(fake()->numberBetween(12, 15), 0);

                $shift = Shift::create([
                    'tenant_id' => $tenant->id,
                    'care_package_id' => $package->id,
                    'carer_id' => $carer->id,
                    'scheduled_start_at' => $start,
                    'scheduled_end_at' => $end,
                    'actual_start_at' => $start,
                    'actual_end_at' => $end,
                    'unpaid_break_minutes' => fake()->randomElement([0, 30]),
                    'status' => ShiftStatus::Completed,
                ]);

                // PARTIAL: only cost ~60% of shifts; the rest stay "not costed".
                if (fake()->boolean(60)) {
                    $shift->cost(CarerRateType::Day); // gracefully no-ops if carer has no rate
                }
            }
        }

        // DP account — PARTIAL: ~1 in 3 active packages have NO account.
        if ($packageStatus === CarePackageStatus::Active && fake()->boolean(66)) {
            $account = DpAccount::create([
                'tenant_id' => $tenant->id,
                'care_package_id' => $package->id,
                'opening_balance' => 0,
                'opened_on' => '2024-09-01',
                'bank_account_ref' => '****' . fake()->numerify('####'),
            ]);

            // A council credit, sometimes a second.
            DpTransaction::create([
                'tenant_id' => $tenant->id,
                'dp_account_id' => $account->id,
                'direction' => TransactionDirection::Credit,
                'amount' => fake()->randomElement([2400, 3685.50, 4900]),
                'transaction_date' => '2024-09-01',
                'source_type' => TransactionSource::CouncilPayment,
                'bank_reference' => 'BACS-' . fake()->numerify('#####'),
            ]);
        }

        // Incidents — scattered, some with no safeguarding referral.
        if (fake()->boolean(30)) {
            Incident::create([
                'tenant_id' => $tenant->id,
                'service_user_id' => $serviceUser->id,
                'severity' => fake()->randomElement(IncidentSeverity::cases()),
                'incident_type' => fake()->randomElement(['Fall', 'Medication error', 'Missed visit', 'Near miss']),
                'description' => fake()->sentence(12),
                'occurred_at' => fake()->dateTimeBetween('-2 months', 'now'),
                'status' => fake()->randomElement(IncidentStatus::cases()),
                // PARTIAL: most have no safeguarding referral (null).
                'safeguarding_referred_at' => fake()->boolean(20) ? fake()->dateTimeBetween('-1 month', 'now') : null,
            ]);
        }
    }

    /* ---------------------------------------------------------------- */
    /* Helpers                                                           */
    /* ---------------------------------------------------------------- */

    protected function splitName(string $full): array
    {
        $parts = preg_split('/\s+/', trim($full));
        $first = array_shift($parts) ?: 'Unknown';
        $last = $parts ? implode(' ', $parts) : 'Person';
        return [$first, $last];
    }
}
