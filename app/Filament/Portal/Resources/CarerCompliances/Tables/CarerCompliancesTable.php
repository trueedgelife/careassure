<?php

namespace App\Filament\Portal\Resources\CarerCompliances\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CarerCompliancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dbs_certificate_number')
                    ->label('DBS number')
                    ->placeholder('—'),
                IconColumn::make('dbs_on_update_service')
                    ->label('On update service')
                    ->boolean(),
                TextColumn::make('right_to_work_expires_on')
                    ->label('Right to work expires')
                    ->date()
                    ->placeholder('—'),
                TextColumn::make('safeguarding_expires_on')
                    ->label('Safeguarding expires')
                    ->date()
                    ->placeholder('—'),
                TextColumn::make('first_aid_expires_on')
                    ->label('First aid expires')
                    ->date()
                    ->placeholder('—'),
                TextColumn::make('moving_handling_expires_on')
                    ->label('Moving & handling expires')
                    ->date()
                    ->placeholder('—'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
