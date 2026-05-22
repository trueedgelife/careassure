<?php

namespace App\Filament\Admin\Resources\ServiceUsers\Pages;

use App\Filament\Admin\Resources\ServiceUsers\ServiceUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceUser extends CreateRecord
{
    protected static string $resource = ServiceUserResource::class;
}
