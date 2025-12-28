<?php

namespace App\Filament\Resources\SeoMasks\Pages;

use App\Filament\Resources\SeoMasks\SeoMaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSeoMask extends EditRecord
{
    protected static string $resource = SeoMaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
