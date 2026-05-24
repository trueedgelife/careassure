<?php

namespace App\Filament\Admin\Resources\Carers\Schemas;

use App\Enums\EmploymentType;
use App\Models\Profile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CarerForm
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
                        modifyQueryUsing: fn ($query) => $query->whereDoesntHave('carer'),
                    )
                    ->getOptionLabelFromRecordUsing(fn (Profile $record) => $record->full_name)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        \Filament\Forms\Components\TextInput::make('first_name')
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('last_name')
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('phone')
                            ->tel(),
                        \Filament\Forms\Components\DatePicker::make('dob')
                            ->label('Date of birth'),
                    ]),
                Select::make('employment_type')
                    ->options(EmploymentType::class)
                    ->default(EmploymentType::Employee)
                    ->required(),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
                Toggle::make('is_primary_carer')
                    ->label('Primary carer')
                    ->default(false),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
