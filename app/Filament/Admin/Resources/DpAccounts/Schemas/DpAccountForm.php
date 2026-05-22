<?php

namespace App\Filament\Admin\Resources\DpAccounts\Schemas;

use App\Models\CarePackage;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DpAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('care_package_id')
                    ->label('Care package')
                    ->relationship(
                        'carePackage',
                        'id',
                        modifyQueryUsing: fn ($query) => $query->whereDoesntHave('dpAccount'),
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (CarePackage $record) => $record->serviceUser?->profile?->full_name
                            ? $record->serviceUser->profile->full_name . " (package #{$record->id})"
                            : "Care package #{$record->id}"
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('opening_balance')
                    ->label('Opening balance')
                    ->numeric()
                    ->prefix('£')
                    ->default(0)
                    ->required(),
                DatePicker::make('opened_on')
                    ->required()
                    ->default(now()),
                DatePicker::make('closed_on')
                    ->helperText('Leave blank while the account is open.'),
                TextInput::make('bank_account_ref')
                    ->label('Bank account reference')
                    ->helperText('Masked reference only — never store full account numbers.'),
            ]);
    }
}
