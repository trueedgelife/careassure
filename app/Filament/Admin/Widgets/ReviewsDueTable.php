<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\CarePackageStatus;
use App\Models\CarePackage;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Carbon;

class ReviewsDueTable extends TableWidget
{
    protected static ?string $heading = 'Care package reviews due';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CarePackage::query()
                    ->where('status', CarePackageStatus::Active)
                    ->with(['serviceUser.profile', 'reviews'])
            )
            ->columns([
                TextColumn::make('serviceUser.profile.full_name')->label('Service user'),
                TextColumn::make('last_review')
                    ->label('Last reviewed')
                    ->state(fn (CarePackage $r) => $r->reviews->max('review_date'))
                    ->date('d/m/Y')
                    ->placeholder('Never'),
                TextColumn::make('next_due')
                    ->label('Next due')
                    ->state(function (CarePackage $r) {
                        $last = $r->reviews->max('review_date');
                        if (! $last) {
                            return null; // never reviewed → placeholder shows
                        }
                        $freq = $r->review_frequency_days ?? 365;
                        return \Illuminate\Support\Carbon::parse($last)->addDays($freq);
                    })
                    ->date('d/m/Y')
                    ->placeholder('Overdue — never reviewed')
                    ->color(function ($state) {
                        if (! $state) {
                            return 'danger'; // never reviewed = overdue
                        }
                        return \Illuminate\Support\Carbon::parse($state)->isPast() ? 'danger' : 'warning';
                    }),
            ])
            ->paginated([10]);
    }
}
