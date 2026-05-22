<?php

namespace App\Filament\Admin\Resources\ServiceUsers;

use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Admin\Resources\ServiceUsers\Pages\CreateServiceUser;
use App\Filament\Admin\Resources\ServiceUsers\Pages\EditServiceUser;
use App\Filament\Admin\Resources\ServiceUsers\Pages\ListServiceUsers;
use App\Filament\Admin\Resources\ServiceUsers\Pages\ViewServiceUser;
use App\Filament\Admin\Resources\ServiceUsers\Schemas\ServiceUserForm;
use App\Filament\Admin\Resources\ServiceUsers\Schemas\ServiceUserInfolist;
use App\Filament\Admin\Resources\ServiceUsers\Tables\ServiceUsersTable;
use App\Filament\Admin\Resources\ServiceUsers\RelationManagers;
use App\Models\ServiceUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ServiceUserResource extends Resource
{
    protected static ?string $model = ServiceUser::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'council_reference';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record) {
            return null;
        }

        $name = $record->profile?->full_name;
        $ref = $record->council_reference;

        return $name
            ? trim($name . ($ref ? " ({$ref})" : ''))
            : "Service user #{$record->id}";
    }

    public static function form(Schema $schema): Schema
    {
        return ServiceUserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ServiceUserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RelationshipsRelationManager::class,
            RelationManagers\CapacityAssessmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceUsers::route('/'),
            'create' => CreateServiceUser::route('/create'),
            'view' => ViewServiceUser::route('/{record}'),
            'edit' => EditServiceUser::route('/{record}/edit'),
        ];
    }

}
