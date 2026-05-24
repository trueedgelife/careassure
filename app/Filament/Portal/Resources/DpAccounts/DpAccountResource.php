<?php

namespace App\Filament\Portal\Resources\DpAccounts;

use App\Filament\Portal\Resources\DpAccounts\Pages\ListDpAccounts;
use App\Filament\Portal\Resources\DpAccounts\Pages\ViewDpAccount;
use App\Filament\Portal\Resources\DpAccounts\Schemas\DpAccountInfolist;
use App\Filament\Portal\Resources\DpAccounts\Tables\DpAccountsTable;
use App\Models\DpAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DpAccountResource extends Resource
{
    protected static ?string $model = DpAccount::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'My Account';

    protected static ?string $modelLabel = 'account';

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

    public static function getEloquentQuery(): Builder
    {
        $ids = auth()->user()?->accessibleServiceUserIds() ?? [];

        return parent::getEloquentQuery()
            ->whereHas('carePackage', fn (Builder $q) => $q->whereIn('service_user_id', $ids))
            ->with('carePackage.serviceUser.profile');
    }

    public static function canAccess(): bool
    {
        return ! empty(auth()->user()?->accessibleServiceUserIds());
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
