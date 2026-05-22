<?php

namespace App\Filament\Admin\Resources\CarePackages\Pages;

use App\Filament\Admin\Resources\CarePackages\CarePackageResource;
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
