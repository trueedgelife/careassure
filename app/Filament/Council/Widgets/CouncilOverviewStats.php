<?php

namespace App\Filament\Council\Widgets;

use App\Enums\CarePackageStatus;
use App\Enums\IncidentStatus;
use App\Enums\ServiceUserStatus;
use App\Models\CarePackage;
use App\Models\Incident;
use App\Models\ServiceUser;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class CouncilOverviewStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $reviewsDue = CarePackage::query()
            ->where('status', CarePackageStatus::Active)
            ->with('reviews')
            ->get()
            ->filter(function (CarePackage $p) {
                $last = $p->reviews->max('review_date');
                $freq = $p->review_frequency_days ?? 365;
                return ! $last || Carbon::parse($last)->addDays($freq)->isPast();
            })
            ->count();

        $openIncidents = Incident::whereIn('status', [IncidentStatus::Open, IncidentStatus::Reviewing])->count();

        $safeguarding = Incident::whereNotNull('safeguarding_referred_at')
            ->whereIn('status', [IncidentStatus::Open, IncidentStatus::Reviewing])
            ->count();

        return [
            Stat::make('Active service users', ServiceUser::where('status', ServiceUserStatus::Active)->count()),
            Stat::make('Active care packages', CarePackage::where('status', CarePackageStatus::Active)->count()),
            Stat::make('Reviews due', $reviewsDue)
                ->description($reviewsDue > 0 ? 'Overdue or never reviewed' : 'All up to date')
                ->color($reviewsDue > 0 ? 'warning' : 'success'),
            Stat::make('Open incidents', $openIncidents)
                ->color($openIncidents > 0 ? 'warning' : 'success'),
            Stat::make('Open safeguarding', $safeguarding)
                ->description($safeguarding > 0 ? 'Referred & still open' : 'None open')
                ->color($safeguarding > 0 ? 'danger' : 'success'),
        ];
    }
}
