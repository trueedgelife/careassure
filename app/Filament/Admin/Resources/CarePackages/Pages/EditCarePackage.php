<?php

namespace App\Filament\Admin\Resources\CarePackages\Pages;

use App\Filament\Admin\Resources\CarePackages\CarePackageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCarePackage extends EditRecord
{
    protected static string $resource = CarePackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
