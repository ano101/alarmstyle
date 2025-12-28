<?php

namespace App\Filament\Resources\CategoryLandings\Pages;

use App\Filament\Resources\CategoryLandings\CategoryLandingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoryLanding extends EditRecord
{
    protected static string $resource = CategoryLandingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
