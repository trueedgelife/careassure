<?php

namespace App\Filament\Admin\Resources\Carers\Pages;

use App\Filament\Admin\Resources\Carers\CarerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCarer extends EditRecord
{
    protected static string $resource = CarerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
