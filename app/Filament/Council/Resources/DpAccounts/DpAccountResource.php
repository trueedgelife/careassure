<?php

namespace App\Filament\Council\Resources\DpAccounts;

use App\Filament\Council\Resources\DpAccounts\Pages\ListDpAccounts;
use App\Filament\Council\Resources\DpAccounts\Pages\ViewDpAccount;
use App\Filament\Council\Resources\DpAccounts\Schemas\DpAccountInfolist;
use App\Filament\Council\Resources\DpAccounts\Tables\DpAccountsTable;
use App\Models\DpAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DpAccountResource extends Resource
{
    protected static ?string $model = DpAccount::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'DP Accounts';

    protected static ?string $modelLabel = 'DP account';

    public static function infolist(Schema $schema): Schema
    {
        return DpAccountInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DpAccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDpAccounts::route('/'),
            'view' => ViewDpAccount::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
