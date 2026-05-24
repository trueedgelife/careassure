<?php

namespace App\Filament\Portal\Resources\CarerRates\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CarerRatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('effective_from', 'desc')
            ->columns([
                TextColumn::make('rate_type')
                    ->badge(),
                TextColumn::make('hourly_rate')
                    ->money('GBP')
                    ->placeholder('—'),
                TextColumn::make('flat_rate')
                    ->money('GBP')
                    ->placeholder('—'),
                TextColumn::make('effective_from')
                    ->date()
                    ->sortable(),
                TextColumn::make('effective_to')
                    ->date()
                    ->placeholder('current'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
