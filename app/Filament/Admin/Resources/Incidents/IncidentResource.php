<?php

namespace App\Filament\Admin\Resources\Incidents;

use App\Filament\Admin\Resources\Incidents\Pages\CreateIncident;
use App\Filament\Admin\Resources\Incidents\Pages\EditIncident;
use App\Filament\Admin\Resources\Incidents\Pages\ListIncidents;
use App\Filament\Admin\Resources\Incidents\Pages\ViewIncident;
use App\Filament\Admin\Resources\Incidents\Schemas\IncidentForm;
use App\Filament\Admin\Resources\Incidents\Schemas\IncidentInfolist;
use App\Filament\Admin\Resources\Incidents\Tables\IncidentsTable;
use App\Models\Incident;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $recordTitleAttribute = 'incident_type';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record) {
            return null;
        }

        $name = $record->serviceUser?->profile?->full_name;
        $type = $record->incident_type;

        return $name
            ? trim($name . ($type ? " — {$type}" : ''))
            : "Incident #{$record->id}";
    }

    public static function form(Schema $schema): Schema
    {
        return IncidentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IncidentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncidentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncidents::route('/'),
            'create' => CreateIncident::route('/create'),
            'view' => ViewIncident::route('/{record}'),
            'edit' => EditIncident::route('/{record}/edit'),
        ];
    }
}
