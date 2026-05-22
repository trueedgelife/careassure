<?php

namespace App\Filament\Admin\Resources\ServiceUsers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServiceUserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('profile.full_name')
                    ->label('Name'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('nhs_number')
                    ->label('NHS number')
                    ->placeholder('—'),
                TextEntry::make('council_reference')
                    ->label('Council reference')
                    ->placeholder('—'),
                TextEntry::make('funding_start_date')
                    ->date()
                    ->placeholder('—'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('—'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('—'),
            ]);
    }
}
