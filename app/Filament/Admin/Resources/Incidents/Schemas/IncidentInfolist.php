<?php

namespace App\Filament\Admin\Resources\Incidents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class IncidentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('serviceUser.profile.full_name')
                    ->label('Service user'),
                TextEntry::make('severity')
                    ->badge(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('incident_type')
                    ->label('Type')
                    ->placeholder('—'),
                TextEntry::make('occurred_at')
                    ->dateTime('d M Y, H:i'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('reportedBy.name')
                    ->label('Reported by')
                    ->placeholder('—'),
                TextEntry::make('safeguarding_referred_at')
                    ->label('Safeguarding referral')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('No referral made'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('—'),
            ]);
    }
}
