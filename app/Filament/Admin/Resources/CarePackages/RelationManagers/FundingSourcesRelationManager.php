<?php

namespace App\Filament\Admin\Resources\CarePackages\RelationManagers;

use App\Enums\FunderType;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FundingSourcesRelationManager extends RelationManager
{
    protected static string $relationship = 'fundingSources';

    protected static ?string $title = 'Funding Sources';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('funder_type')
                    ->options(FunderType::class)
                    ->required(),
                TextInput::make('funder_name')
                    ->label('Funder name'),
                TextInput::make('weekly_amount')
                    ->label('Weekly amount (£)')
                    ->numeric()
                    ->prefix('£')
                    ->required(),
                DatePicker::make('effective_from')
                    ->required()
                    ->default(now()),
                DatePicker::make('effective_to')
                    ->label('Effective to')
                    ->helperText('Leave blank if still current.'),
                TextInput::make('reference')
                    ->helperText('Funder case/reference number.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('funder_name')
            ->defaultSort('effective_from', 'desc')
            ->columns([
                TextColumn::make('funder_type')
                    ->badge(),
                TextColumn::make('funder_name')
                    ->placeholder('—'),
                TextColumn::make('weekly_amount')
                    ->money('GBP')
                    ->sortable(),
                TextColumn::make('effective_from')
                    ->date()
                    ->sortable(),
                TextColumn::make('effective_to')
                    ->date()
                    ->placeholder('current'),
                TextColumn::make('reference')
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
