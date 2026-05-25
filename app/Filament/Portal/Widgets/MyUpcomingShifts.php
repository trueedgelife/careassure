<?php

namespace App\Filament\Portal\Widgets;

use App\Enums\ShiftStatus;
use App\Models\Shift;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Carbon;

class MyUpcomingShifts extends TableWidget
{
    protected static ?string $heading = 'My upcoming shifts';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->carer() !== null;
    }

    public function table(Table $table): Table
    {
        $carerId = auth()->user()?->carer()?->id;

        return $table
            ->query(
                Shift::query()
                    ->where('carer_id', $carerId)
                    ->where('status', ShiftStatus::Scheduled)
                    ->where('scheduled_start_at', '>=', Carbon::now()->startOfDay())
                    ->with('carePackage.serviceUser.profile')
                    ->orderBy('scheduled_start_at')
            )
            ->columns([
                TextColumn::make('scheduled_start_at')->label('Date')->date('d/m/Y')->sortable(),
                TextColumn::make('start_time')->label('Start')
                    ->state(fn ($record) => Carbon::parse($record->scheduled_start_at)->format('H:i')),
                TextColumn::make('end_time')->label('End')
                    ->state(fn ($record) => Carbon::parse($record->scheduled_end_at)->format('H:i')),
                TextColumn::make('carePackage.serviceUser.profile.full_name')->label('For')->placeholder('—'),
                TextColumn::make('support_category')->label('Type')->placeholder('—'),
            ])
            ->paginated([10]);
    }
}
