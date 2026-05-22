<?php

namespace App\Filament\Admin\Resources\Carers\RelationManagers;

use App\Enums\CarerRateType;
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

class RatesRelationManager extends RelationManager
{
    protected static string $relationship = 'rates';

    protected static ?string $title = 'Pay Rates';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('rate_type')
                    ->options(CarerRateType::class)
                    ->default(CarerRateType::Day)
                    ->required(),
                TextInput::make('hourly_rate')
                    ->label('Hourly rate (£)')
                    ->numeric()
                    ->prefix('£')
                    ->helperText('For time-based pay (day, night, weekend).'),
                TextInput::make('flat_rate')
                    ->label('Flat rate (£)')
                    ->numeric()
                    ->prefix('£')
                    ->helperText('For fixed sums, e.g. sleep-ins.'),
                DatePicker::make('effective_from')
                    ->required()
                    ->default(now()),
                DatePicker::make('effective_to')
                    ->label('Effective to')
                    ->helperText('Leave blank if this rate is still current.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('rate_type')
            ->defaultSort('effective_from', 'desc')
            ->columns([
                TextColumn::make('rate_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('hourly_rate')
                    ->money('GBP')
                    ->placeholder('—'),
                TextColumn::make('flat_rate')
                    ->money('GBP')
                    ->placeholder('—'),
                TextColumn::make('effective_from')
                    ->date()
                    ->sortable(),
                TextColumn::make('effective_to')
                    ->date()
                    ->placeholder('current'),
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
