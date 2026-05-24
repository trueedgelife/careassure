<?php

namespace App\Filament\Portal\Resources\DpAccounts\Pages;

use App\Filament\Portal\Resources\DpAccounts\DpAccountResource;
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
