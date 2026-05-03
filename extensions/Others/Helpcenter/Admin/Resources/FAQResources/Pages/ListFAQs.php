<?php

namespace Paymenter\Extensions\Others\Helpcenter\Admin\Resources\FAQResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Others\Helpcenter\Admin\Resources\FAQResource;

class ListFAQs extends ListRecords
{
    protected static string $resource = FAQResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
