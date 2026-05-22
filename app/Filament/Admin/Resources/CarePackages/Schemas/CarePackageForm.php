<?php

namespace App\Filament\Admin\Resources\CarePackages\Schemas;

use App\Enums\CarePackageStatus;
use App\Models\ServiceUser;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CarePackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_user_id')
                    ->label('Service user')
                    ->relationship('serviceUser', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn (ServiceUser $record) => $record->profile?->full_name ?? "Service user #{$record->id}"
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('weekly_funded_hours')
                    ->label('Weekly funded hours')
                    ->numeric()
                    ->suffix('hrs'),
                TextInput::make('annual_budget')
                    ->label('Annual budget')
                    ->numeric()
                    ->prefix('£'),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
                TextInput::make('review_frequency_days')
                    ->label('Review frequency (days)')
                    ->numeric()
                    ->default(365)
                    ->required(),
                Select::make('status')
                    ->options(CarePackageStatus::class)
                    ->default(CarePackageStatus::Draft)
                    ->required(),
            ]);
    }
}
