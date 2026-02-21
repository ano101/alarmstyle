<?php

namespace App\Filament\Resources\CategoryLandings\Pages;

use App\Filament\Resources\CategoryLandings\CategoryLandingResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCategoryLanding extends EditRecord
{
    protected static string $resource = CategoryLandingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_on_site')
                ->label('Открыть на сайте')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(fn () => $this->record->load(['category.slug'])->url)
                ->openUrlInNewTab()
                ->visible(fn () => (bool) $this->record->load(['category.slug'])->url),
            DeleteAction::make(),
        ];
    }
}
