<?php

namespace App\Filament\Admin\Resources\Carers\Pages;

use App\Filament\Admin\Resources\Carers\CarerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCarer extends ViewRecord
{
    protected static string $resource = CarerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
