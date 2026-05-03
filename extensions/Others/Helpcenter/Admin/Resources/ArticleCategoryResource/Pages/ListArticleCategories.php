<?php

namespace Paymenter\Extensions\Others\Helpcenter\Admin\Resources\ArticleCategoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Others\Helpcenter\Admin\Resources\ArticleCategoryResource;

class ListArticleCategories extends ListRecords
{
    protected static string $resource = ArticleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
