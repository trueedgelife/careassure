<?php

namespace App\Filament\Council\Resources\DpAccounts\Pages;

use App\Filament\Council\Resources\DpAccounts\DpAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDpAccounts extends ListRecords
{
    protected static string $resource = DpAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
