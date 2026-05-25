<?php

namespace App\Filament\Portal\Widgets;

use App\Models\CarerCompliance;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class MyComplianceStatus extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->carer() !== null;
    }

    protected function getStats(): array
    {
        $carerId = auth()->user()?->carer()?->id;
        $compliance = CarerCompliance::where('carer_id', $carerId)->first();

        if (! $compliance) {
            return [
                Stat::make('Compliance', 'Not on file')
                    ->description('No compliance record yet — contact your coordinator')
                    ->color('warning'),
            ];
        }

        $stats = [];
        $items = [
            'DBS (on update service)' => $compliance->dbs_on_update_service ? 'Yes' : 'Check',
            'Safeguarding' => $compliance->safeguarding_expires_on,
            'First aid' => $compliance->first_aid_expires_on,
            'Right to work' => $compliance->right_to_work_expires_on,
        ];

        foreach ($items as $label => $value) {
            if ($value instanceof \DateTimeInterface || (is_string($value) && strtotime($value))) {
                $date = Carbon::parse($value);
                $expired = $date->isPast();
                $soon = ! $expired && $date->lessThanOrEqualTo(Carbon::now()->addDays(30));
                $stats[] = Stat::make($label, $date->format('d/m/Y'))
                    ->description($expired ? 'Expired' : ($soon ? 'Expiring soon' : 'Current'))
                    ->color($expired ? 'danger' : ($soon ? 'warning' : 'success'));
            } else {
                $stats[] = Stat::make($label, (string) $value);
            }
        }

        return $stats;
    }
}
