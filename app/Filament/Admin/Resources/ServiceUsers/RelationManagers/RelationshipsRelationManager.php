<?php

namespace App\Filament\Admin\Resources\ServiceUsers\RelationManagers;

use App\Enums\LpaType;
use App\Enums\RelationshipType;
use App\Models\Profile;
use App\Filament\Admin\Resources\ServiceUsers\RelationManagers;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RelationshipsRelationManager extends RelationManager
{
    protected static string $relationship = 'relationships';

    protected static ?string $title = 'Relationships';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('related_profile_id')
                    ->label('Person')
                    ->relationship('relatedProfile', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Profile $record) => $record->full_name)
                    ->searchable(['first_name', 'last_name'])
                    ->preload()
                    ->required(),
                Select::make('relationship_type')
                    ->options(RelationshipType::class)
                    ->required(),
                Toggle::make('is_emergency_contact')
                    ->label('Emergency contact')
                    ->default(false),
                Toggle::make('has_lpa')
                    ->label('Holds LPA')
                    ->live()
                    ->default(false),
                Select::make('lpa_type')
                    ->label('LPA type')
                    ->options(LpaType::class)
                    ->visible(fn ($get) => $get('has_lpa')),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('related_profile_id')
            ->modifyQueryUsing(fn ($query) => $query->with('relatedProfile'))
            ->columns([
                TextColumn::make('relatedProfile.full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('relationship_type')
                    ->badge(),
                IconColumn::make('is_emergency_contact')
                    ->label('Emergency')
                    ->boolean(),
                IconColumn::make('has_lpa')
                    ->label('LPA')
                    ->boolean(),
                TextColumn::make('lpa_type')
                    ->label('LPA type')
                    ->badge()
                    ->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RelationshipsRelationManager::class,
            RelationManagers\CapacityAssessmentsRelationManager::class,
        ];
    }

}
