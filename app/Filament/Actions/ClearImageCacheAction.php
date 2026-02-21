<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ClearImageCacheAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'clearImageCache';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Очистить кэш изображений');

        $this->icon(Heroicon::OutlinedTrash);

        $this->color('danger');

        $this->requiresConfirmation();

        $this->modalHeading('Очистить кэш изображений');

        $this->modalDescription('Вы уверены, что хотите очистить кэш обработанных изображений? Это действие нельзя отменить.');

        $this->modalSubmitActionLabel('Да, очистить');

        $this->modalCancelActionLabel('Отмена');

        $this->action(function () {
            try {
                $disk = Storage::disk(config('images.disk'));
                $cachePath = config('images.cache_path');

                if (!$disk->exists($cachePath)) {
                    Notification::make()
                        ->title('Кэш изображений пуст')
                        ->info()
                        ->send();

                    return;
                }

                $files = $disk->allFiles($cachePath);
                $count = count($files);

                if ($count === 0) {
                    Notification::make()
                        ->title('Кэш изображений пуст')
                        ->info()
                        ->send();

                    return;
                }

                Artisan::call('images:clear-cache', ['--force' => true]);

                Notification::make()
                    ->title('Кэш изображений успешно очищен')
                    ->body("Удалено файлов: {$count}")
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Ошибка при очистке кэша')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        });
    }
}

