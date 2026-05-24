<?php

namespace App\Filament\Portal\Resources\CarerCompliances\Pages;

use App\Filament\Portal\Resources\CarerCompliances\CarerComplianceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCarerCompliance extends ViewRecord
{
    protected static string $resource = CarerComplianceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
