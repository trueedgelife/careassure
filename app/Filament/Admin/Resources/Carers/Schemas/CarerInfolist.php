<?php

namespace App\Filament\Admin\Resources\Carers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CarerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('profile.full_name')
                    ->label('Name'),
                TextEntry::make('employment_type')
                    ->badge(),
                IconEntry::make('is_primary_carer')
                    ->label('Primary carer')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('start_date')
                    ->date()
                    ->placeholder('—'),
                TextEntry::make('end_date')
                    ->date()
                    ->placeholder('—'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('—'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('—'),
            ]);
    }
}
