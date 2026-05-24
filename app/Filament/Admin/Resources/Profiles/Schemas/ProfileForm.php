<?php

namespace App\Filament\Admin\Resources\Profiles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                DatePicker::make('dob')
                    ->label('Date of birth'),
                TextInput::make('address_line_1')
                    ->label('Address line 1'),
                TextInput::make('address_line_2')
                    ->label('Address line 2'),
                TextInput::make('city'),
                TextInput::make('postcode'),
                TextInput::make('emergency_contact_name'),
                TextInput::make('emergency_contact_phone')
                    ->tel(),
            ]);
    }
}
