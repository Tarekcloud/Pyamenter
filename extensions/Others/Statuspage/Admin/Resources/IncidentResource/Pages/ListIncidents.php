<?php

namespace Paymenter\Extensions\Others\Statuspage\Admin\Resources\IncidentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\IncidentResource;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
