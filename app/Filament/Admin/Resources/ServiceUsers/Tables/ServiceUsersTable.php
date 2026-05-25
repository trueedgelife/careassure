<?php

namespace App\Filament\Council\Resources\ServiceUsers\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($q) => $q->with('profile'))
            ->columns([
                TextColumn::make('profile.full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('council_reference')
                    ->label('Council ref')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('nhs_number')
                    ->label('NHS number')
                    ->placeholder('—'),
                TextColumn::make('funding_start_date')
                    ->label('Funding since')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
