<?php

namespace App\Filament\Admin\Resources\CarePackages;

use App\Filament\Admin\Resources\CarePackages\RelationManagers;
use App\Filament\Admin\Resources\CarePackages\Pages\CreateCarePackage;
use App\Filament\Admin\Resources\CarePackages\Pages\EditCarePackage;
use App\Filament\Admin\Resources\CarePackages\Pages\ListCarePackages;
use App\Filament\Admin\Resources\CarePackages\Pages\ViewCarePackage;
use App\Filament\Admin\Resources\CarePackages\Schemas\CarePackageForm;
use App\Filament\Admin\Resources\CarePackages\Schemas\CarePackageInfolist;
use App\Filament\Admin\Resources\CarePackages\Tables\CarePackagesTable;
use App\Models\CarePackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CarePackageResource extends Resource
{
    protected static ?string $model = CarePackage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record) {
            return null;
        }

        $name = $record->serviceUser?->profile?->full_name;
        $status = $record->status?->getLabel();

        return $name
            ? trim($name . ($status ? " — {$status}" : ''))
            : "Care package #{$record->id}";
    }

    public static function form(Schema $schema): Schema
    {
        return CarePackageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CarePackageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarePackagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FundingSourcesRelationManager::class,
            RelationManagers\ShiftsRelationManager::class,
            RelationManagers\ReviewsRelationManager::class,
            RelationManagers\ExpensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarePackages::route('/'),
            'create' => CreateCarePackage::route('/create'),
            'view' => ViewCarePackage::route('/{record}'),
            'edit' => EditCarePackage::route('/{record}/edit'),
        ];
    }
}
