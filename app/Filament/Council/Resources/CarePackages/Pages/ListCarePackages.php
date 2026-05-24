<?php

namespace App\Filament\Council\Resources\CarePackages\Pages;

use App\Filament\Council\Resources\CarePackages\CarePackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarePackages extends ListRecords
{
    protected static string $resource = CarePackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
