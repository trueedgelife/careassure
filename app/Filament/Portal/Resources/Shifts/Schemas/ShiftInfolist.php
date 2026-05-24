<?php

namespace App\Filament\Portal\Resources\Shifts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ShiftInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('carePackage.serviceUser.profile.full_name')
                    ->label('Care for'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('scheduled_start_at')
                    ->label('Scheduled start')
                    ->dateTime('D j M Y, H:i'),
                TextEntry::make('scheduled_end_at')
                    ->label('Scheduled end')
                    ->dateTime('D j M Y, H:i'),
                TextEntry::make('actual_start_at')
                    ->label('Actual start')
                    ->dateTime('D j M Y, H:i')
                    ->placeholder('—'),
                TextEntry::make('actual_end_at')
                    ->label('Actual end')
                    ->dateTime('D j M Y, H:i')
                    ->placeholder('—'),
                TextEntry::make('unpaid_break_minutes')
                    ->label('Unpaid break (mins)'),
                TextEntry::make('support_category')
                    ->label('Type')
                    ->placeholder('—'),
                TextEntry::make('notes')
                    ->placeholder('—')
                    ->columnSpanFull(),
            ]);
    }
}
