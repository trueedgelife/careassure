<?php

namespace App\Filament\Admin\Resources\DpAccounts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DpAccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('carePackage.serviceUser.profile.full_name')
                    ->label('Service user'),
                TextEntry::make('balance')
                    ->label('Current balance')
                    ->state(fn ($record) => $record->balance())
                    ->money('GBP')
                    ->weight('bold')
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),
                TextEntry::make('committed_wages')
                    ->label('Committed wages (costed shifts)')
                    ->state(fn ($record) => $record->committedWages())
                    ->money('GBP'),
                TextEntry::make('opening_balance')
                    ->money('GBP'),
                TextEntry::make('opened_on')
                    ->date(),
                TextEntry::make('closed_on')
                    ->date()
                    ->placeholder('Account open'),
                TextEntry::make('bank_account_ref')
                    ->label('Bank reference')
                    ->placeholder('—'),
            ]);
    }
}
