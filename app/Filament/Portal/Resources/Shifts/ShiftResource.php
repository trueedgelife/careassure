<?php

namespace App\Filament\Portal\Resources\Shifts;

use App\Filament\Portal\Resources\Shifts\Pages\ListShifts;
use App\Filament\Portal\Resources\Shifts\Pages\ViewShift;
use App\Filament\Portal\Resources\Shifts\Schemas\ShiftInfolist;
use App\Filament\Portal\Resources\Shifts\Tables\ShiftsTable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Shift;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'My Shifts';

    protected static ?string $modelLabel = 'shift';

    public static function infolist(Schema $schema): Schema
    {
        return ShiftInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShiftsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShifts::route('/'),
            'view' => ViewShift::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $carerId = auth()->user()?->carer()?->id;

        return parent::getEloquentQuery()
            ->where('carer_id', $carerId)
            ->with(['carePackage.serviceUser.profile']);
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
