<?php

namespace App\Filament\Portal\Resources\Shifts\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShiftsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_start_at', 'desc')
            ->columns([
                TextColumn::make('scheduled_start_at')
                    ->label('Date')
                    ->date('D j M Y')
                    ->sortable(),
                TextColumn::make('scheduled_start_at')
                    ->label('Start')
                    ->time('H:i'),
                TextColumn::make('scheduled_end_at')
                    ->label('End')
                    ->time('H:i'),
                TextColumn::make('carePackage.serviceUser.profile.full_name')
                    ->label('For')
                    ->placeholder('—'),
                TextColumn::make('support_category')
                    ->label('Type')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
