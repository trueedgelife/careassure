<?php

namespace App\Filament\Admin\Resources\Profiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['carer', 'serviceUser', 'user']))
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['last_name', 'first_name']),
                IconColumn::make('is_service_user')
                    ->label('Service user')
                    ->boolean()
                    ->state(fn ($record) => $record->serviceUser !== null),
                IconColumn::make('is_carer')
                    ->label('Carer')
                    ->boolean()
                    ->state(fn ($record) => $record->carer !== null),
                IconColumn::make('has_login')
                    ->label('Login')
                    ->boolean()
                    ->state(fn ($record) => $record->user_id !== null),
                TextColumn::make('phone')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('city')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('dob')
                    ->label('Date of birth')
                    ->date()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \App\Filament\Admin\Resources\Profiles\ProfileResource::grantLoginAction(),
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
