<?php

namespace App\Filament\Admin\Resources\Carers;

use App\Filament\Admin\Resources\Carers\RelationManagers;
use App\Filament\Admin\Resources\Carers\Pages\CreateCarer;
use App\Filament\Admin\Resources\Carers\Pages\EditCarer;
use App\Filament\Admin\Resources\Carers\Pages\ListCarers;
use App\Filament\Admin\Resources\Carers\Pages\ViewCarer;
use App\Filament\Admin\Resources\Carers\Schemas\CarerForm;
use App\Filament\Admin\Resources\Carers\Schemas\CarerInfolist;
use App\Filament\Admin\Resources\Carers\Tables\CarersTable;
use App\Models\Carer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CarerResource extends Resource
{
    protected static ?string $model = Carer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record?->profile?->full_name ?? "Carer #{$record?->id}";
    }

    public static function form(Schema $schema): Schema
    {
        return CarerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CarerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RatesRelationManager::class,
            RelationManagers\ComplianceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarers::route('/'),
            'create' => CreateCarer::route('/create'),
            'view' => ViewCarer::route('/{record}'),
            'edit' => EditCarer::route('/{record}/edit'),
        ];
    }
}
