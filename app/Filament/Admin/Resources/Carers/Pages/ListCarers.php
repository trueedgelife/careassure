<?php

namespace App\Filament\Admin\Resources\Carers\Pages;

use App\Filament\Admin\Resources\Carers\CarerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarers extends ListRecords
{
    protected static string $resource = CarerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
