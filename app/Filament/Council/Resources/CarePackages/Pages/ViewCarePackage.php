<?php

namespace App\Filament\Council\Resources\CarePackages\Pages;

use App\Filament\Council\Resources\CarePackages\CarePackageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCarePackage extends ViewRecord
{
    protected static string $resource = CarePackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
