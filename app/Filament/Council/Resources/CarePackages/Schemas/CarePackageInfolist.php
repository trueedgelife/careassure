<?php

namespace App\Filament\Council\Resources\CarePackages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CarePackageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('serviceUser.id')
                    ->label('Service user'),
                TextEntry::make('delivery_model')
                    ->badge(),
                TextEntry::make('weekly_funded_hours')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('annual_budget')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('start_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('end_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('review_frequency_days')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
