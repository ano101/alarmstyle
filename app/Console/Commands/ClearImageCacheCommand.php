<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearImageCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:clear-cache {--force : Force clearing without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистить кэш обработанных изображений';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = Storage::disk(config('images.disk'));
        $cachePath = config('images.cache_path');

        if (!$disk->exists($cachePath)) {
            $this->info('Кэш изображений пуст.');

            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Вы уверены, что хотите очистить кэш изображений?')) {
            $this->info('Операция отменена.');

            return self::SUCCESS;
        }

        try {
            $files = $disk->allFiles($cachePath);
            $count = count($files);

            if ($count === 0) {
                $this->info('Кэш изображений пуст.');

                return self::SUCCESS;
            }

            $this->info("Удаление {$count} файлов из кэша...");

            $disk->deleteDirectory($cachePath);
            $disk->makeDirectory($cachePath);

            $this->info("✓ Успешно удалено {$count} файлов.");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Ошибка при очистке кэша: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
