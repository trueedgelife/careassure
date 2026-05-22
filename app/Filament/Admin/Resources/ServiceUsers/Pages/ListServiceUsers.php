<?php

namespace App\Filament\Admin\Resources\ServiceUsers\Pages;

use App\Filament\Admin\Resources\ServiceUsers\ServiceUserResource;
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
