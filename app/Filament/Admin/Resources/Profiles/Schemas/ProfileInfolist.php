<?php

namespace App\Filament\Admin\Resources\Profiles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Person')
                    ->schema([
                        TextEntry::make('full_name')->label('Name'),
                        TextEntry::make('dob')->label('Date of birth')->date()->placeholder('—'),
                        TextEntry::make('phone')->placeholder('—'),
                        TextEntry::make('city')->placeholder('—'),
                        TextEntry::make('postcode')->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('Roles in the care system')
                    ->description('What this person is within this council.')
                    ->schema([
                        IconEntry::make('is_service_user')
                            ->label('Service user')
                            ->boolean()
                            ->state(fn ($record) => $record->serviceUser !== null),
                        IconEntry::make('is_carer')
                            ->label('Carer')
                            ->boolean()
                            ->state(fn ($record) => $record->carer !== null),
                        TextEntry::make('serviceUser.status')
                            ->label('Service user status')
                            ->badge()
                            ->visible(fn ($record) => $record->serviceUser !== null),
                        TextEntry::make('carer.employment_type')
                            ->label('Employment type')
                            ->badge()
                            ->visible(fn ($record) => $record->carer !== null),
                    ])
                    ->columns(2),

                Section::make('Login & access')
                    ->schema([
                        IconEntry::make('has_login')
                            ->label('Has a login')
                            ->boolean()
                            ->state(fn ($record) => $record->user_id !== null),
                        TextEntry::make('user.email')
                            ->label('Login email')
                            ->placeholder('No login — this person cannot sign in')
                            ->visible(fn ($record) => $record->user_id !== null),
                    ])
                    ->columns(2),

                Section::make('Emergency contact')
                    ->schema([
                        TextEntry::make('emergency_contact_name')->label('Name')->placeholder('—'),
                        TextEntry::make('emergency_contact_phone')->label('Phone')->placeholder('—'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
