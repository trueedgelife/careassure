<?php

namespace App\Filament\Admin\Resources\ServiceUsers\RelationManagers;

use App\Filament\Admin\Resources\ServiceUsers\RelationManagers;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CapacityAssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'capacityAssessments';

    protected static ?string $title = 'Capacity Assessments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('decision_domain')
                    ->label('Decision domain')
                    ->placeholder('e.g. financial, care_arrangements, medical')
                    ->required(),
                Toggle::make('has_capacity')
                    ->label('Has capacity for this decision')
                    ->default(false),
                TextInput::make('assessed_by')
                    ->label('Assessed by')
                    ->placeholder('Name / role of assessor'),
                DatePicker::make('assessed_on')
                    ->required(),
                DatePicker::make('review_due')
                    ->label('Review due'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('decision_domain')
            ->columns([
                TextColumn::make('decision_domain')
                    ->label('Domain')
                    ->searchable(),
                IconColumn::make('has_capacity')
                    ->label('Has capacity')
                    ->boolean(),
                TextColumn::make('assessed_by')
                    ->label('Assessed by')
                    ->placeholder('—'),
                TextColumn::make('assessed_on')
                    ->date()
                    ->sortable(),
                TextColumn::make('review_due')
                    ->date()
                    ->sortable()
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
