<?php

namespace App\Filament\Council\Resources\CarePackages;

use App\Filament\Council\Resources\CarePackages\Pages\ListCarePackages;
use App\Filament\Council\Resources\CarePackages\Pages\ViewCarePackage;
use App\Filament\Council\Resources\CarePackages\Schemas\CarePackageInfolist;
use App\Filament\Council\Resources\CarePackages\Tables\CarePackagesTable;
use App\Models\CarePackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CarePackageResource extends Resource
{
    protected static ?string $model = CarePackage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Care Packages';

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
