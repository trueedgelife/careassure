<?php

namespace App\Filament\Portal\Resources\DpAccounts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DpAccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('carePackage.id')
                    ->label('Care package'),
                TextEntry::make('opening_balance')
                    ->numeric(),
                TextEntry::make('opened_on')
                    ->date(),
                TextEntry::make('closed_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('bank_account_ref')
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
