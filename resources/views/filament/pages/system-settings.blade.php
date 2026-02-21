<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                Управление кэшем изображений
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Очистка кэша обработанных изображений может освободить место на диске.
                После очистки изображения будут создаваться заново при первом запросе.
            </p>
            <div class="mt-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Используйте кнопку "Очистить кэш изображений" в правом верхнем углу страницы.
                </p>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                Путь к кэшу изображений
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ config('images.cache_path') }}
            </p>
        </div>
    </div>
</x-filament-panels::page>

