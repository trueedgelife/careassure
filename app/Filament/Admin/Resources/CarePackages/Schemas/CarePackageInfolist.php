<?php

namespace App\Filament\Admin\Resources\CarePackages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CarePackageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('serviceUser.profile.full_name')
                    ->label('Service user'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('weekly_funded_hours')
                    ->label('Weekly funded hours')
                    ->suffix(' hrs')
                    ->placeholder('—'),
                TextEntry::make('annual_budget')
                    ->money('GBP')
                    ->placeholder('—'),
                TextEntry::make('start_date')
                    ->date()
                    ->placeholder('—'),
                TextEntry::make('end_date')
                    ->date()
                    ->placeholder('—'),
                TextEntry::make('review_frequency_days')
                    ->label('Review frequency (days)'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('—'),
            ]);
    }
}
