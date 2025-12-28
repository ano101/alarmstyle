<?php

namespace App\Filament\Resources\CategoryRequiredAttributes\Pages;

use App\Filament\Resources\CategoryRequiredAttributes\CategoryRequiredAttributeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategoryRequiredAttributes extends ListRecords
{
    protected static string $resource = CategoryRequiredAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
