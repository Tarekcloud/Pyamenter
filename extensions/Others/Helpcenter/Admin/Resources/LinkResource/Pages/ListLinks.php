<?php

namespace Paymenter\Extensions\Others\Helpcenter\Admin\Resources\LinkResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
use Paymenter\Extensions\Others\Helpcenter\Admin\Resources\LinkResource;

class ListLinks extends ListRecords
{
    protected static string $resource = LinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
