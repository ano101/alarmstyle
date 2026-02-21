<?php

namespace App\Filament\Pages;

use App\Filament\Actions\ClearImageCacheAction;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SystemSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected string $view = 'filament.pages.system-settings';

    protected static ?string $navigationLabel = 'Системные настройки';

    protected static ?string $title = 'Системные настройки';

    protected static ?int $navigationSort = 99;

    public static function getNavigationGroup(): ?string
    {
        return 'Система';
    }

    protected function getHeaderActions(): array
    {
        return [
            ClearImageCacheAction::make(),
        ];
    }
}

