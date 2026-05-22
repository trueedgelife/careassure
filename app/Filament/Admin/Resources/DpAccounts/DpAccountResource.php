<?php

namespace App\Filament\Admin\Resources\DpAccounts;

use App\Filament\Admin\Resources\DpAccounts\RelationManagers;
use App\Filament\Admin\Resources\DpAccounts\Pages\CreateDpAccount;
use App\Filament\Admin\Resources\DpAccounts\Pages\EditDpAccount;
use App\Filament\Admin\Resources\DpAccounts\Pages\ListDpAccounts;
use App\Filament\Admin\Resources\DpAccounts\Pages\ViewDpAccount;
use App\Filament\Admin\Resources\DpAccounts\Schemas\DpAccountForm;
use App\Filament\Admin\Resources\DpAccounts\Schemas\DpAccountInfolist;
use App\Filament\Admin\Resources\DpAccounts\Tables\DpAccountsTable;
use App\Models\DpAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class DpAccountResource extends Resource
{
    protected static ?string $model = DpAccount::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'DP account';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record) {
            return null;
        }

        $name = $record->carePackage?->serviceUser?->profile?->full_name;

        return $name
            ? "{$name} — DP Account"
            : "DP Account #{$record->id}";
    }

    public static function form(Schema $schema): Schema
    {
        return DpAccountForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DpAccountInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DpAccountsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDpAccounts::route('/'),
            'create' => CreateDpAccount::route('/create'),
            'view' => ViewDpAccount::route('/{record}'),
            'edit' => EditDpAccount::route('/{record}/edit'),
        ];
    }
}
