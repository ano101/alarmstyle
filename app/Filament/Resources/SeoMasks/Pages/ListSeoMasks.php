<?php

namespace App\Filament\Resources\SeoMasks\Pages;

use App\Filament\Resources\SeoMasks\SeoMaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSeoMasks extends ListRecords
{
    protected static string $resource = SeoMaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
