<?php

namespace App\Filament\Council\Resources\DpAccounts\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DpAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($q) => $q->with('carePackage.serviceUser.profile'))
            ->columns([
                TextColumn::make('carePackage.serviceUser.profile.full_name')
                    ->label('Service user')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('balance')
                    ->label('Current balance')
                    ->state(fn ($record) => $record->balance())
                    ->money('GBP')
                    ->weight('bold')
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),
                TextColumn::make('opened_on')
                    ->date('d/m/Y')
                    ->placeholder('—'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
