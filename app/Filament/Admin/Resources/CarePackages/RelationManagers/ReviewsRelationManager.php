<?php

namespace App\Filament\Admin\Resources\CarePackages\RelationManagers;

use App\Enums\CarePackageReviewOutcome;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    protected static ?string $title = 'Reviews';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('review_date')
                    ->required()
                    ->default(now()),
                TextInput::make('reviewer_name')
                    ->label('Reviewer name'),
                TextInput::make('reviewer_organisation')
                    ->label('Reviewer organisation'),
                Select::make('outcome')
                    ->options(CarePackageReviewOutcome::class),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('review_date')
            ->defaultSort('review_date', 'desc')
            ->columns([
                TextColumn::make('review_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('reviewer_name')
                    ->placeholder('—'),
                TextColumn::make('reviewer_organisation')
                    ->label('Organisation')
                    ->placeholder('—'),
                TextColumn::make('outcome')
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
}
