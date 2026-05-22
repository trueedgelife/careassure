<?php

namespace App\Filament\Admin\Resources\Incidents\Pages;

use App\Filament\Admin\Resources\Incidents\IncidentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIncident extends CreateRecord
{
    protected static string $resource = IncidentResource::class;
}
