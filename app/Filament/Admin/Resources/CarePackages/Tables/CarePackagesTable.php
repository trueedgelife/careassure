<?php

namespace App\Filament\Council\Resources\CarePackages\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CarePackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($q) => $q->with('serviceUser.profile'))
            ->columns([
                TextColumn::make('serviceUser.profile.full_name')
                    ->label('Service user')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('delivery_model')
                    ->label('Delivery')
                    ->badge()
                    ->sortable(),
                TextColumn::make('weekly_funded_hours')
                    ->label('Weekly hrs')
                    ->suffix(' hrs')
                    ->placeholder('—'),
                TextColumn::make('annual_budget')
                    ->money('GBP')
                    ->placeholder('—'),
                TextColumn::make('start_date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
