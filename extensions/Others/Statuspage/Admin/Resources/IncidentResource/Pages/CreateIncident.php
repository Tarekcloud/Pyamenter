<?php

namespace Paymenter\Extensions\Others\Statuspage\Admin\Resources\IncidentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\IncidentResource;

class CreateIncident extends CreateRecord
{
    protected static string $resource = IncidentResource::class;
}
