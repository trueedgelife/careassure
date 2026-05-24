<?php

namespace App\Filament\Council\Resources\ServiceUsers\Pages;

use App\Filament\Council\Resources\ServiceUsers\ServiceUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceUsers extends ListRecords
{
    protected static string $resource = ServiceUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
