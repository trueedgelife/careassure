<?php

namespace App\Filament\Portal\Resources\CarerRates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CarerRateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('carer.id')
                    ->label('Carer'),
                TextEntry::make('rate_type')
                    ->badge(),
                TextEntry::make('hourly_rate')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('flat_rate')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('effective_from')
                    ->date(),
                TextEntry::make('effective_to')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
