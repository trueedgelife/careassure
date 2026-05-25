<?php

namespace App\Filament\Council\Resources\Incidents\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncidentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($q) => $q->with('serviceUser.profile'))
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                TextColumn::make('serviceUser.profile.full_name')
                    ->label('Service user')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('occurred_at')
                    ->label('Occurred')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('incident_type')
                    ->label('Type')
                    ->placeholder('—'),
                TextColumn::make('severity')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                IconColumn::make('safeguarding_referred_at')
                    ->label('Safeguarding')
                    ->boolean()
                    ->state(fn ($record) => $record->safeguarding_referred_at !== null),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
