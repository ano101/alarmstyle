<?php

namespace App\Filament\Resources\CategoryLandings\Pages;

use App\Filament\Resources\CategoryLandings\CategoryLandingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategoryLandings extends ListRecords
{
    protected static string $resource = CategoryLandingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
