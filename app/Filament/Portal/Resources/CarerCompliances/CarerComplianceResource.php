<?php

namespace App\Filament\Portal\Resources\CarerCompliances;

use App\Filament\Portal\Resources\CarerCompliances\Pages\ListCarerCompliances;
use App\Filament\Portal\Resources\CarerCompliances\Pages\ViewCarerCompliance;
use App\Filament\Portal\Resources\CarerCompliances\Schemas\CarerComplianceInfolist;
use App\Filament\Portal\Resources\CarerCompliances\Tables\CarerCompliancesTable;
use App\Models\CarerCompliance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CarerComplianceResource extends Resource
{
    protected static ?string $model = CarerCompliance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'My Compliance';

    protected static ?string $modelLabel = 'compliance record';

    public static function infolist(Schema $schema): Schema
    {
        return CarerComplianceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarerCompliancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarerCompliances::route('/'),
            'view' => ViewCarerCompliance::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $carerId = auth()->user()?->carer()?->id;

        return parent::getEloquentQuery()->where('carer_id', $carerId);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->carer() !== null;
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
