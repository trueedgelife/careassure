<?php

namespace App\Filament\Portal\Resources\CarerRates;

use App\Filament\Portal\Resources\CarerRates\Pages\ListCarerRates;
use App\Filament\Portal\Resources\CarerRates\Pages\ViewCarerRate;
use App\Filament\Portal\Resources\CarerRates\Schemas\CarerRateInfolist;
use App\Filament\Portal\Resources\CarerRates\Tables\CarerRatesTable;
use App\Models\CarerRate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CarerRateResource extends Resource
{
    protected static ?string $model = CarerRate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-pound';

    protected static ?string $navigationLabel = 'My Rates';

    protected static ?string $modelLabel = 'rate';

    public static function infolist(Schema $schema): Schema
    {
        return CarerRateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarerRatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarerRates::route('/'),
            'view' => ViewCarerRate::route('/{record}'),
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
