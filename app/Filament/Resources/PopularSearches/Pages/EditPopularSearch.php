<?php

namespace App\Filament\Resources\PopularSearches\Pages;

use App\Filament\Resources\PopularSearches\PopularSearchResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPopularSearch extends EditRecord
{
    protected static string $resource = PopularSearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
