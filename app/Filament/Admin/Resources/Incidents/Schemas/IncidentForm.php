<?php

namespace App\Filament\Admin\Resources\Incidents\Schemas;

use App\Enums\IncidentSeverity;
use App\Enums\IncidentStatus;
use App\Models\ServiceUser;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class IncidentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_user_id')
                    ->label('Service user')
                    ->relationship('serviceUser', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn (ServiceUser $record) => $record->profile?->full_name ?? "Service user #{$record->id}"
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('severity')
                    ->options(IncidentSeverity::class)
                    ->required(),
                TextInput::make('incident_type')
                    ->label('Incident type')
                    ->placeholder('e.g. Fall, Medication error, Missed visit'),
                DateTimePicker::make('occurred_at')
                    ->label('Occurred at')
                    ->seconds(false)
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(IncidentStatus::class)
                    ->default(IncidentStatus::Open)
                    ->required(),
                DateTimePicker::make('safeguarding_referred_at')
                    ->label('Safeguarding referral made at')
                    ->seconds(false)
                    ->helperText('Leave blank unless a formal safeguarding referral was made.'),
            ]);
    }
}
