<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SyncMeilisearchCommand extends Command
{
    protected $signature = 'meilisearch:sync
                            {--no-import : Не импортировать данные, только синхронизировать настройки}
                            {--flush : Очистить индекс перед импортом}';

    protected $description = 'Синхронизация настроек индексов Meilisearch и импорт моделей';

    public function handle(): int
    {
        $this->info('🔄 Начинаем синхронизацию с Meilisearch...');
        $this->newLine();

        // 1. Синхронизация настроек индекса
        $this->info('📋 Синхронизация настроек индекса...');

        $exitCode = Artisan::call('scout:sync-index-settings', [], $this->output);

        if ($exitCode !== 0) {
            $this->error('❌ Ошибка при синхронизации настроек индекса');

            return self::FAILURE;
        }

        $this->info('✅ Настройки индекса синхронизированы');
        $this->newLine();

        // 2. Импорт данных (если не указан флаг --no-import)
        if (! $this->option('no-import')) {
            // Очистка индекса (если указан флаг --flush)
            if ($this->option('flush')) {
                $this->warn('🗑️  Очистка индекса products...');
                Artisan::call('scout:flush', [
                    'model' => Product::class,
                ], $this->output);
                $this->info('✅ Индекс очищен');
                $this->newLine();
            }

            $this->info('📥 Импорт моделей Product...');

            $exitCode = Artisan::call('scout:import', [
                'model' => Product::class,
            ], $this->output);

            if ($exitCode !== 0) {
                $this->error('❌ Ошибка при импорте данных');

                return self::FAILURE;
            }

            $this->info('✅ Данные успешно импортированы');
        } else {
            $this->comment('⏭️  Импорт данных пропущен (флаг --no-import)');
        }

        $this->newLine();
        $this->info('🎉 Синхронизация завершена успешно!');

        return self::SUCCESS;
    }
}
