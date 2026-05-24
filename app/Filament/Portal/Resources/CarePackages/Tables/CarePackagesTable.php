<?php

namespace App\Filament\Portal\Resources\CarePackages\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CarePackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serviceUser.profile.full_name')
                    ->label('Care for'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('delivery_model')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('weekly_funded_hours')
                    ->label('Weekly hours')
                    ->suffix(' hrs')
                    ->placeholder('—'),
                TextColumn::make('start_date')
                    ->date()
                    ->placeholder('—'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
