<?php

namespace App\Filament\Portal\Resources\CarerRates\Pages;

use App\Filament\Portal\Resources\CarerRates\CarerRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarerRates extends ListRecords
{
    protected static string $resource = CarerRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
