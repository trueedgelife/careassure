<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\IncidentStatus;
use App\Enums\ServiceUserStatus;
use App\Enums\CarePackageStatus;
use App\Models\Carer;
use App\Models\CarerCompliance;
use App\Models\CarePackage;
use App\Models\Incident;
use App\Models\ServiceUser;
use App\Models\Shift;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class OverviewStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $soon = Carbon::now()->addDays(30);

        // Compliance items expiring within 30 days or already expired.
        $expiringCompliance = CarerCompliance::query()
            ->where(function ($q) use ($soon) {
                foreach ([
                             'right_to_work_expires_on',
                             'moving_handling_expires_on',
                             'safeguarding_expires_on',
                             'first_aid_expires_on',
                             'medication_expires_on',
                         ] as $field) {
                    $q->orWhere(fn ($sub) => $sub->whereNotNull($field)->where($field, '<=', $soon));
                }
            })
            ->count();

        // Reviews due: active packages whose latest review + frequency has passed,
        // or which have never been reviewed.
        $reviewsDue = CarePackage::query()
            ->where('status', CarePackageStatus::Active)
            ->get()
            ->filter(fn (CarePackage $p) => static::reviewIsDue($p))
            ->count();

        $uncostedShifts = Shift::query()
            ->whereNull('computed_cost')
            ->whereNotNull('actual_end_at')
            ->count();

        $openIncidents = Incident::query()
            ->whereIn('status', [IncidentStatus::Open, IncidentStatus::Reviewing])
            ->count();

        return [
            Stat::make('Active service users', ServiceUser::where('status', ServiceUserStatus::Active)->count()),
            Stat::make('Active care packages', CarePackage::where('status', CarePackageStatus::Active)->count()),
            Stat::make('Active carers', Carer::where('is_active', true)->count()),
            Stat::make('Compliance expiring', $expiringCompliance)
                ->description($expiringCompliance > 0 ? 'Within 30 days or expired' : 'All current')
                ->color($expiringCompliance > 0 ? 'danger' : 'success'),
            Stat::make('Reviews due', $reviewsDue)
                ->description($reviewsDue > 0 ? 'Overdue or never reviewed' : 'All up to date')
                ->color($reviewsDue > 0 ? 'warning' : 'success'),
            Stat::make('Open incidents', $openIncidents)
                ->color($openIncidents > 0 ? 'warning' : 'success'),
        ];
    }

    /** A package is due if it has no review, or last review + frequency days has passed. */
    public static function reviewIsDue(CarePackage $package): bool
    {
        $last = $package->reviews()->max('review_date');
        $freq = $package->review_frequency_days ?? 365;

        if (! $last) {
            return true; // never reviewed
        }

        return Carbon::parse($last)->addDays($freq)->isPast();
    }
}
