<?php

namespace App\Filament\Portal\Resources\CarerCompliances\Pages;

use App\Filament\Portal\Resources\CarerCompliances\CarerComplianceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarerCompliances extends ListRecords
{
    protected static string $resource = CarerComplianceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
