<?php

namespace App\Filament\Admin\Resources\ServiceUsers\Schemas;

use App\Enums\ServiceUserStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('profile_id')
                    ->label('Person')
                    ->relationship(
                        'profile',
                        'first_name',
                        modifyQueryUsing: fn ($query) => $query->whereDoesntHave('serviceUser'),
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'last_name'])
                    ->preload()
                    ->required(),
                TextInput::make('nhs_number'),
                TextInput::make('council_reference'),
                DatePicker::make('funding_start_date'),
                Select::make('status')
                    ->options(ServiceUserStatus::class)
                    ->default(ServiceUserStatus::Assessment)
                    ->required(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
