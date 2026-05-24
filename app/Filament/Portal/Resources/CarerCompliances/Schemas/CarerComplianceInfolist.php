<?php

namespace App\Filament\Portal\Resources\CarerCompliances\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CarerComplianceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('carer.id')
                    ->label('Carer'),
                TextEntry::make('dbs_certificate_number')
                    ->placeholder('-'),
                TextEntry::make('dbs_issued_on')
                    ->date()
                    ->placeholder('-'),
                IconEntry::make('dbs_on_update_service')
                    ->boolean(),
                TextEntry::make('right_to_work_verified_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('right_to_work_expires_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('references_completed_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('moving_handling_expires_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('safeguarding_expires_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('first_aid_expires_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('medication_expires_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
