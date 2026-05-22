<?php

namespace App\Filament\Admin\Resources\Incidents\Tables;

use App\Enums\IncidentSeverity;
use App\Enums\IncidentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class IncidentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('serviceUser.profile'))
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                TextColumn::make('serviceUser.profile.full_name')
                    ->label('Service user')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('severity')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('incident_type')
                    ->label('Type')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('occurred_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                IconColumn::make('safeguarding_referred_at')
                    ->label('Referred')
                    ->boolean()
                    ->state(fn ($record) => $record->safeguarding_referred_at !== null),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(IncidentStatus::class),
                SelectFilter::make('severity')
                    ->options(IncidentSeverity::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
