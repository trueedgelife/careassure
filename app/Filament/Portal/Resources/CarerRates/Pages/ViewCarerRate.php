<?php

namespace App\Filament\Portal\Resources\CarerRates\Pages;

use App\Filament\Portal\Resources\CarerRates\CarerRateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCarerRate extends ViewRecord
{
    protected static string $resource = CarerRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
