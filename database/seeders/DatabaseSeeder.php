<?php

namespace Database\Seeders;

use App\Enums\LpaType;
use App\Enums\RelationshipType;
use App\Enums\ServiceUserStatus;
use App\Enums\CarerRateType;
use App\Enums\EmploymentType;
use App\Enums\CarePackageReviewOutcome;
use App\Enums\CarePackageStatus;
use App\Enums\ConfirmationType;
use App\Enums\FunderType;
use App\Enums\IncidentSeverity;
use App\Enums\IncidentStatus;
use App\Enums\ShiftStatus;
use App\Enums\ExpenseCategory;
use App\Enums\TransactionDirection;
use App\Enums\TransactionSource;
use App\Models\DpAccount;
use App\Models\DpTransaction;
use App\Models\Expense;
use App\Models\CarePackage;
use App\Models\CarePackageReview;
use App\Models\FundingSource;
use App\Models\Incident;
use App\Models\Shift;
use App\Models\ShiftConfirmation;
use App\Models\Carer;
use App\Models\CarerCompliance;
use App\Models\CarerRate;
use App\Models\CapacityAssessment;
use App\Models\PersonRelationship;
use App\Models\ServiceUser;
use App\Models\Profile;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            RoleSeeder::class,
        ]);

        $demoTenant = Tenant::where('slug', 'demo-council')->firstOrFail();

        // ---- Platform super admin ----
        $super = User::firstOrCreate(
            ['tenant_id' => null, 'email' => 'super@trueedge.life'],
            [
                'name' => 'Platform Super Admin',
                'password' => Hash::make('password'),
                'is_super_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Super admin gets the super_admin role with no tenant context.
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);
        $super->syncRoles(['super_admin']);

        // ---- Demo council admin ----
        $admin = User::firstOrCreate(
            ['tenant_id' => $demoTenant->id, 'email' => 'admin@demo-council.local'],
            [
                'name' => 'Demo Council Admin',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        Profile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'tenant_id' => $demoTenant->id,
                'first_name' => 'Demo',
                'last_name' => 'Admin',
            ]
        );

        // Assign tenant_admin role within the demo tenant's context.
        app(PermissionRegistrar::class)->setPermissionsTeamId($demoTenant->id);
        $admin->syncRoles(['tenant_admin']);

        // ---- Demo council reviewer ----
        $reviewer = User::firstOrCreate(
            ['tenant_id' => $demoTenant->id, 'email' => 'reviewer@demo-council.local'],
            [
                'name' => 'Demo Council Reviewer',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        Profile::firstOrCreate(
            ['user_id' => $reviewer->id],
            [
                'tenant_id' => $demoTenant->id,
                'first_name' => 'Demo',
                'last_name' => 'Reviewer',
            ]
        );

        app(PermissionRegistrar::class)->setPermissionsTeamId($demoTenant->id);
        $reviewer->syncRoles(['council_reviewer']);

        // ---- Demo service user: Margaret Hughes ----
        $marProfile = Profile::firstOrCreate(
            ['tenant_id' => $demoTenant->id, 'first_name' => 'Margaret', 'last_name' => 'Hughes'],
            [
                'dob' => '1948-03-12',
                'address_line_1' => '14 Elm Court',
                'city' => 'Cardiff',
                'postcode' => 'CF10 1AA',
            ]
        );

        $serviceUser = ServiceUser::firstOrCreate(
            ['profile_id' => $marProfile->id],
            [
                'tenant_id' => $demoTenant->id,
                'nhs_number' => '943 476 5919',
                'council_reference' => 'CCC-2024-0142',
                'funding_start_date' => '2024-09-01',
                'status' => ServiceUserStatus::Active,
                'is_active' => true,
            ]
        );

        // Her daughter Sarah — emergency contact, holds Health & Welfare LPA
        $daughterProfile = Profile::firstOrCreate(
            ['tenant_id' => $demoTenant->id, 'first_name' => 'Sarah', 'last_name' => 'Hughes'],
            ['phone' => '07700 900123', 'city' => 'Cardiff']
        );

        PersonRelationship::firstOrCreate(
            ['service_user_id' => $serviceUser->id, 'related_profile_id' => $daughterProfile->id],
            [
                'tenant_id' => $demoTenant->id,
                'relationship_type' => RelationshipType::Child,
                'is_emergency_contact' => true,
                'has_lpa' => true,
                'lpa_type' => LpaType::HealthWelfare,
                'notes' => 'Primary contact; holds Health & Welfare LPA.',
            ]
        );

        CapacityAssessment::firstOrCreate(
            ['service_user_id' => $serviceUser->id, 'decision_domain' => 'financial'],
            [
                'tenant_id' => $demoTenant->id,
                'has_capacity' => false,
                'assessed_by' => 'J. Okafor (Social Worker)',
                'assessed_on' => '2024-08-20',
                'review_due' => '2025-08-20',
                'notes' => 'Lacks capacity for complex financial decisions; daughter manages via LPA.',
            ]
        );

        // ---- Demo carer: Tom Davies ----
        $carerProfile = Profile::firstOrCreate(
            ['tenant_id' => $demoTenant->id, 'first_name' => 'Tom', 'last_name' => 'Davies'],
            ['phone' => '07700 900456', 'city' => 'Cardiff']
        );

        $carer = Carer::firstOrCreate(
            ['profile_id' => $carerProfile->id],
            [
                'tenant_id' => $demoTenant->id,
                'employment_type' => EmploymentType::Employee,
                'start_date' => '2024-09-01',
                'is_primary_carer' => true,
                'is_active' => true,
            ]
        );

        CarerRate::firstOrCreate(
            ['carer_id' => $carer->id, 'rate_type' => CarerRateType::Day->value, 'effective_from' => '2024-09-01'],
            [
                'tenant_id' => $demoTenant->id,
                'hourly_rate' => 13.50,
            ]
        );

        CarerRate::firstOrCreate(
            ['carer_id' => $carer->id, 'rate_type' => CarerRateType::SleepIn->value, 'effective_from' => '2024-09-01'],
            [
                'tenant_id' => $demoTenant->id,
                'flat_rate' => 65.00,
            ]
        );

        CarerCompliance::firstOrCreate(
            ['carer_id' => $carer->id],
            [
                'tenant_id' => $demoTenant->id,
                'dbs_certificate_number' => '001234567890',
                'dbs_issued_on' => '2024-08-15',
                'dbs_on_update_service' => true,
                'right_to_work_verified_on' => '2024-08-20',
                'safeguarding_expires_on' => '2026-08-15',
                'moving_handling_expires_on' => '2026-08-15',
                'first_aid_expires_on' => '2026-02-15',
            ]
        );

        // ---- Care package for Margaret ----
        $carePackage = CarePackage::firstOrCreate(
            ['service_user_id' => $serviceUser->id, 'start_date' => '2024-09-01'],
            [
                'tenant_id' => $demoTenant->id,
                'weekly_funded_hours' => 21.00,
                'annual_budget' => 14742.00,
                'review_frequency_days' => 365,
                'status' => CarePackageStatus::Active,
            ]
        );

        // Dual funding: Local Authority + NHS Continuing Healthcare
        FundingSource::firstOrCreate(
            ['care_package_id' => $carePackage->id, 'funder_type' => FunderType::LocalAuthority->value, 'effective_from' => '2024-09-01'],
            [
                'tenant_id' => $demoTenant->id,
                'funder_name' => 'Cardiff Council',
                'weekly_amount' => 189.00,
                'reference' => 'CCC-2024-0142',
            ]
        );

        FundingSource::firstOrCreate(
            ['care_package_id' => $carePackage->id, 'funder_type' => FunderType::NhsChc->value, 'effective_from' => '2024-09-01'],
            [
                'tenant_id' => $demoTenant->id,
                'funder_name' => 'Cardiff & Vale ICB',
                'weekly_amount' => 94.50,
                'reference' => 'CHC-78421',
            ]
        );

        // ---- Two shifts worked by Tom ----
        // A normal daytime shift: 9am–2pm with a 30-min unpaid break = 4.5 paid hours.
        $dayShift = Shift::firstOrCreate(
            [
                'care_package_id' => $carePackage->id,
                'carer_id' => $carer->id,
                'scheduled_start_at' => '2025-01-06 09:00:00',
            ],
            [
                'tenant_id' => $demoTenant->id,
                'scheduled_end_at' => '2025-01-06 14:00:00',
                'actual_start_at' => '2025-01-06 09:00:00',
                'actual_end_at' => '2025-01-06 14:00:00',
                'unpaid_break_minutes' => 30,
                'support_category' => 'Personal care & companionship',
                'status' => ShiftStatus::Completed,
                'created_by' => $admin->id,
            ]
        );

        // A sleep-in: 10pm–7am crossing midnight (the datetime design earns its keep here).
        $sleepInShift = Shift::firstOrCreate(
            [
                'care_package_id' => $carePackage->id,
                'carer_id' => $carer->id,
                'scheduled_start_at' => '2025-01-06 22:00:00',
            ],
            [
                'tenant_id' => $demoTenant->id,
                'scheduled_end_at' => '2025-01-07 07:00:00',
                'actual_start_at' => '2025-01-06 22:00:00',
                'actual_end_at' => '2025-01-07 07:00:00',
                'unpaid_break_minutes' => 0,
                'support_category' => 'Overnight sleep-in',
                'status' => ShiftStatus::Completed,
                'created_by' => $admin->id,
            ]
        );

        // Carer confirms the day shift
        ShiftConfirmation::firstOrCreate(
            ['shift_id' => $dayShift->id, 'confirmed_by' => $admin->id],
            [
                'tenant_id' => $demoTenant->id,
                'confirmation_type' => ConfirmationType::Carer,
                'confirmed_at' => '2025-01-06 14:05:00',
                'notes' => 'Confirmed by Tom on completion.',
            ]
        );

        // ---- A care package review ----
        CarePackageReview::firstOrCreate(
            ['care_package_id' => $carePackage->id, 'review_date' => '2025-09-01'],
            [
                'tenant_id' => $demoTenant->id,
                'reviewer_name' => 'J. Okafor',
                'reviewer_organisation' => 'Cardiff Council Adult Services',
                'outcome' => CarePackageReviewOutcome::Continue,
                'notes' => 'Package working well; no change to funded hours.',
            ]
        );

        // ---- An incident ----
        Incident::firstOrCreate(
            ['service_user_id' => $serviceUser->id, 'occurred_at' => '2024-12-18 16:30:00'],
            [
                'tenant_id' => $demoTenant->id,
                'reported_by' => $admin->id,
                'severity' => IncidentSeverity::Medium,
                'incident_type' => 'Fall',
                'description' => 'Margaret had a minor fall in the kitchen; no injury, GP informed as a precaution.',
                'status' => IncidentStatus::Resolved,
            ]
        );

        // ---- Direct payment account for Margaret's package ----
        $dpAccount = DpAccount::firstOrCreate(
            ['care_package_id' => $carePackage->id],
            [
                'tenant_id' => $demoTenant->id,
                'opening_balance' => 0,
                'opened_on' => '2024-09-01',
                'bank_account_ref' => '****4821',
            ]
        );

        // Council pays a quarter's funding in (credit)
        DpTransaction::firstOrCreate(
            [
                'dp_account_id' => $dpAccount->id,
                'source_type' => TransactionSource::CouncilPayment->value,
                'transaction_date' => '2024-09-01',
            ],
            [
                'tenant_id' => $demoTenant->id,
                'direction' => TransactionDirection::Credit,
                'amount' => 3685.50,
                'bank_reference' => 'BACS-CCC-Q1',
                'notes' => 'Q1 direct payment from Cardiff Council (13 weeks).',
                'created_by' => $admin->id,
            ]
        );

        // A payroll-service expense — the observer auto-posts a matching debit
        Expense::firstOrCreate(
            [
                'care_package_id' => $carePackage->id,
                'category' => ExpenseCategory::PayrollService->value,
                'expense_date' => '2024-09-15',
            ],
            [
                'tenant_id' => $demoTenant->id,
                'amount' => 45.00,
                'supplier_name' => 'PayPacket Ltd',
                'notes' => 'Monthly payroll administration.',
                'created_by' => $admin->id,
            ]
        );

        // A payroll-service expense — the observer auto-posts a matching debit
        Expense::firstOrCreate(
            [
                'care_package_id' => $carePackage->id,
                'category' => ExpenseCategory::PayrollService->value,
                'expense_date' => '2024-09-15',
            ],
            [
                'tenant_id' => $demoTenant->id,
                'amount' => 45.00,
                'supplier_name' => 'PayPacket Ltd',
                'notes' => 'Monthly payroll administration.',
                'created_by' => $admin->id,
            ]
        );

        // The five demo councils with uneven data (runs last — needs roles + tenants already seeded).
        $this->call(DemoDataSeeder::class);

    }

}
