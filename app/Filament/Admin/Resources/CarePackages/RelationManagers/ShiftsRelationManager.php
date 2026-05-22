<?php

namespace App\Filament\Admin\Resources\CarePackages\RelationManagers;

use App\Enums\CarerRateType;
use App\Enums\ShiftStatus;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ShiftsRelationManager extends RelationManager
{
    protected static string $relationship = 'shifts';

    protected static ?string $title = 'Shifts';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('carer_id')
                    ->relationship('carer', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->profile?->full_name ?? "Carer #{$record->id}"
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('scheduled_start_at')
                    ->label('Scheduled start')
                    ->seconds(false)
                    ->required(),
                DateTimePicker::make('scheduled_end_at')
                    ->label('Scheduled end')
                    ->seconds(false)
                    ->required(),
                DateTimePicker::make('actual_start_at')
                    ->label('Actual start')
                    ->seconds(false),
                DateTimePicker::make('actual_end_at')
                    ->label('Actual end')
                    ->seconds(false),
                TextInput::make('unpaid_break_minutes')
                    ->label('Unpaid break (minutes)')
                    ->numeric()
                    ->default(0),
                TextInput::make('support_category')
                    ->label('Support category'),
                Select::make('status')
                    ->options(ShiftStatus::class)
                    ->default(ShiftStatus::Scheduled)
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('support_category')
            ->modifyQueryUsing(fn ($query) => $query->with('carer.profile'))
            ->defaultSort('scheduled_start_at', 'desc')
            ->columns([
                TextColumn::make('carer.profile.full_name')
                    ->label('Carer'),
                TextColumn::make('scheduled_start_at')
                    ->label('Start')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('scheduled_end_at')
                    ->label('End')
                    ->dateTime('d M Y, H:i'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('computed_cost')
                    ->label('Cost')
                    ->money('GBP')
                    ->placeholder('not costed'),
            ])
            ->recordActions([
                Action::make('cost')
                    ->label('Cost')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Select::make('rate_type')
                            ->label('Rate type')
                            ->options(CarerRateType::class)
                            ->default(CarerRateType::Day)
                            ->required(),
                    ])
                    ->action(function (Model $record, array $data) {
                        $record->cost(CarerRateType::from($data['rate_type']));
                    })
                    ->modalHeading('Cost this shift')
                    ->modalSubmitActionLabel('Calculate & save'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
