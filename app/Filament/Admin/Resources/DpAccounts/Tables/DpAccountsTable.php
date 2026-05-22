<?php

namespace App\Filament\Admin\Resources\DpAccounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DpAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('carePackage.serviceUser.profile'))
            ->columns([
                TextColumn::make('carePackage.serviceUser.profile.full_name')
                    ->label('Service user')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('balance')
                    ->label('Balance')
                    ->state(fn ($record) => $record->balance())
                    ->money('GBP')
                    ->weight('bold')
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),
                TextColumn::make('opening_balance')
                    ->money('GBP')
                    ->toggleable(),
                TextColumn::make('opened_on')
                    ->date()
                    ->sortable(),
                TextColumn::make('closed_on')
                    ->date()
                    ->placeholder('open'),
                TextColumn::make('bank_account_ref')
                    ->label('Bank ref')
                    ->placeholder('—'),
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
