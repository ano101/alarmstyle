<?php

namespace App\Filament\Resources\CategoryRequiredAttributes\Pages;

use App\Filament\Resources\CategoryRequiredAttributes\CategoryRequiredAttributeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoryRequiredAttribute extends EditRecord
{
    protected static string $resource = CategoryRequiredAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
