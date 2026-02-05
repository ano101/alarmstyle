<?php

namespace App\Filament\Resources\PopularSearches\Pages;

use App\Filament\Resources\PopularSearches\PopularSearchResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPopularSearch extends ViewRecord
{
    protected static string $resource = PopularSearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
