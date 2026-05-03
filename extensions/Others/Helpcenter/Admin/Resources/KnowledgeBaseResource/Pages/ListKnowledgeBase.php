<?php

namespace Paymenter\Extensions\Others\Helpcenter\Admin\Resources\KnowledgeBaseResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Others\Helpcenter\Admin\Resources\KnowledgeBaseResource;

class ListKnowledgeBase extends ListRecords
{
    protected static string $resource = KnowledgeBaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
