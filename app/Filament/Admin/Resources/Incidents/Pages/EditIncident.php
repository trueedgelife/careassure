<?php

namespace App\Filament\Admin\Resources\Incidents\Pages;

use App\Filament\Admin\Resources\Incidents\IncidentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIncident extends EditRecord
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
