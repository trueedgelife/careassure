<?php

namespace App\Filament\Council\Resources\ServiceUsers\Pages;

use App\Filament\Council\Resources\ServiceUsers\ServiceUserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceUser extends ViewRecord
{
    protected static string $resource = ServiceUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
