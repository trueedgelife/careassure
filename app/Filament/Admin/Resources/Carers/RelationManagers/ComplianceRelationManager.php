<?php

namespace App\Filament\Admin\Resources\Carers\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ComplianceRelationManager extends RelationManager
{
    protected static string $relationship = 'compliance';

    protected static ?string $title = 'Compliance';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('dbs_certificate_number')
                    ->label('DBS certificate number'),
                DatePicker::make('dbs_issued_on')
                    ->label('DBS issued on'),
                Toggle::make('dbs_on_update_service')
                    ->label('On DBS Update Service')
                    ->default(false),
                DatePicker::make('right_to_work_verified_on')
                    ->label('Right to work verified on'),
                DatePicker::make('right_to_work_expires_on')
                    ->label('Right to work expires on'),
                DatePicker::make('references_completed_on')
                    ->label('References completed on'),
                DatePicker::make('safeguarding_expires_on')
                    ->label('Safeguarding training expires'),
                DatePicker::make('moving_handling_expires_on')
                    ->label('Moving & handling expires'),
                DatePicker::make('first_aid_expires_on')
                    ->label('First aid expires'),
                DatePicker::make('medication_expires_on')
                    ->label('Medication training expires'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('dbs_certificate_number')
            ->columns([
                TextColumn::make('dbs_certificate_number')
                    ->label('DBS number')
                    ->placeholder('—'),
                IconColumn::make('dbs_on_update_service')
                    ->label('Update service')
                    ->boolean(),
                TextColumn::make('right_to_work_expires_on')
                    ->label('RTW expires')
                    ->date()
                    ->placeholder('—'),
                TextColumn::make('safeguarding_expires_on')
                    ->label('Safeguarding')
                    ->date()
                    ->placeholder('—'),
                TextColumn::make('first_aid_expires_on')
                    ->label('First aid')
                    ->date()
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
