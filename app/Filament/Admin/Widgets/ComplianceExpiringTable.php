<?php

namespace App\Filament\Admin\Widgets;

use App\Models\CarerCompliance;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ComplianceExpiringTable extends TableWidget
{
    protected static ?string $heading = 'Compliance expiring soon';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $soon = Carbon::now()->addDays(30);

        $fields = [
            'right_to_work_expires_on',
            'moving_handling_expires_on',
            'safeguarding_expires_on',
            'first_aid_expires_on',
            'medication_expires_on',
        ];

        return $table
            ->query(
                CarerCompliance::query()
                    ->with('carer.profile')
                    ->where(function (Builder $q) use ($fields, $soon) {
                        foreach ($fields as $field) {
                            $q->orWhere(fn ($sub) => $sub->whereNotNull($field)->where($field, '<=', $soon));
                        }
                    })
            )
            ->columns([
                TextColumn::make('carer.profile.full_name')
                    ->label('Carer'),
                TextColumn::make('safeguarding_expires_on')->label('Safeguarding')->date('d/m/Y')->placeholder('—')
                    ->color(fn ($state) => $state && \Illuminate\Support\Carbon::parse($state)->isPast() ? 'danger' : null),
                TextColumn::make('first_aid_expires_on')->label('First aid')->date('d/m/Y')->placeholder('—')
                    ->color(fn ($state) => $state && \Illuminate\Support\Carbon::parse($state)->isPast() ? 'danger' : null),
                TextColumn::make('moving_handling_expires_on')->label('Moving & handling')->date('d/m/Y')->placeholder('—')
                    ->color(fn ($state) => $state && \Illuminate\Support\Carbon::parse($state)->isPast() ? 'danger' : null),
                TextColumn::make('right_to_work_expires_on')->label('Right to work')->date('d/m/Y')->placeholder('—')
                    ->color(fn ($state) => $state && \Illuminate\Support\Carbon::parse($state)->isPast() ? 'danger' : null),
            ])
            ->paginated([10]);
    }
}
