<?php

namespace App\Filament\Resources\PopularSearches\Pages;

use App\Filament\Resources\PopularSearches\PopularSearchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPopularSearches extends ListRecords
{
    protected static string $resource = PopularSearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
