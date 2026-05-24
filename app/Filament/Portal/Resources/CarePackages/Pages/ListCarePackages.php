<?php

namespace App\Filament\Portal\Resources\CarePackages\Pages;

use App\Filament\Portal\Resources\CarePackages\CarePackageResource;
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
