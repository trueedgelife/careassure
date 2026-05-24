<?php

namespace App\Filament\Portal\Resources\CarePackages;

use App\Filament\Portal\Resources\CarePackages\Pages\ListCarePackages;
use App\Filament\Portal\Resources\CarePackages\Pages\ViewCarePackage;
use App\Filament\Portal\Resources\CarePackages\Schemas\CarePackageInfolist;
use App\Filament\Portal\Resources\CarePackages\Tables\CarePackagesTable;
use App\Models\CarePackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CarePackageResource extends Resource
{
    protected static ?string $model = CarePackage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'My Care';

    protected static ?string $modelLabel = 'care package';

    public static function infolist(Schema $schema): Schema
    {
        return CarePackageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarePackagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarePackages::route('/'),
            'view' => ViewCarePackage::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $ids = auth()->user()?->accessibleServiceUserIds() ?? [];

        return parent::getEloquentQuery()
            ->whereIn('service_user_id', $ids)
            ->with(['serviceUser.profile', 'fundingSources']);
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
