<?php

namespace App\Filament\Council\Resources\ServiceUsers;

use App\Filament\Council\Resources\ServiceUsers\Pages\ListServiceUsers;
use App\Filament\Council\Resources\ServiceUsers\Pages\ViewServiceUser;
use App\Filament\Council\Resources\ServiceUsers\Schemas\ServiceUserInfolist;
use App\Filament\Council\Resources\ServiceUsers\Tables\ServiceUsersTable;
use App\Models\ServiceUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ServiceUserResource extends Resource
{
    protected static ?string $model = ServiceUser::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Service Users';

    public static function infolist(Schema $schema): Schema
    {
        return ServiceUserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceUsersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceUsers::route('/'),
            'view' => ViewServiceUser::route('/{record}'),
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
