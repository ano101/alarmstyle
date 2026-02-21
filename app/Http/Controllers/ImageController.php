<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;

class ImageController
{
    protected ImageManager $manager;

    public function __construct()
    {
        // Если хочешь Imagick → поменяй Driver() на new \Intervention\Image\Drivers\Imagick\Driver()
        $this->manager = new ImageManager(new Driver);
    }

    /**
     * URL: /img/{preset}/{path...}
     * preset: product.card, category.banner и т.п.
     * path:   products/door1.jpg, categories/doors/front.png и т.д.
     */
    public function __invoke(string $preset, string $path, Request $request)
    {
        $config = config('images');

        // --- 1. Разбор группового пресета ---
        [$group, $name] = $this->parsePreset($preset);

        $presets = $config['presets'] ?? [];

        if (! isset($presets[$group][$name])) {
            abort(404, "Preset {$group}.{$name} not found");
        }

        $presetConfig = $presets[$group][$name];

        // Мини-защита от ../
        $path = ltrim(str_replace('..', '', $path), '/');

        $disk = $config['disk'] ?? 'public';
        $sourceRoot = $config['source_path'] ?? 'images';
        $cacheRoot = $config['cache_path'] ?? 'images/cache';
        $mainFormat = $config['format'] ?? 'webp';
        $fallback = $config['fallback_format'] ?? 'jpeg';
        $quality = $config['quality'] ?? 80;
        $cacheTtlDays = $config['cache_ttl_days'] ?? null;

        // --- 2. Определяем, поддерживает ли браузер webp ---
        $acceptHeader = $request->header('Accept', '');
        $acceptsWebp = str_contains($acceptHeader, 'image/webp');

        $targetFormat = ($mainFormat === 'webp' && ! $acceptsWebp)
            ? $fallback
            : $mainFormat;

        // --- 3. Путь к оригиналу ---
        $sourceRoot = $config['source_path'] ?? 'images';

        // Если path уже начинается с sourceRoot, не добавляем его ещё раз
        if ($sourceRoot && str_starts_with($path, $sourceRoot.'/')) {
            $sourcePath = $path;
        } elseif ($sourceRoot) {
            $sourcePath = "{$sourceRoot}/{$path}";
        } else {
            $sourcePath = $path;
        }

        if (! Storage::disk($disk)->exists($sourcePath)) {
            abort(404, 'Original image not found');
        }

        $sourceFull = Storage::disk($disk)->path($sourcePath);

        // --- 4. Путь к кэшу с зеркальной структурой ---
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $dir = pathinfo($path, PATHINFO_DIRNAME);
        if ($dir == '.' || $dir == DIRECTORY_SEPARATOR) {
            $dir = '';
        }

        // Хэш для обновления при изменении пресетов
        $hash = md5(json_encode([
            $group,
            $name,
            $path,
            $presetConfig,
            $targetFormat,
        ]));

        $cacheDirRelative = trim($cacheRoot, '/')
            .'/'.$group
            .'/'.$name;

        if ($dir) {
            $cacheDirRelative .= '/'.trim($dir, '/');
        }

        $cachePathRelative = sprintf(
            '%s/%s-%s.%s',
            $cacheDirRelative,
            $filename,
            $hash,
            $targetFormat
        );

        $cacheFull = Storage::disk('public')->path($cachePathRelative);

        // --- 5. Если кэш существует и актуален — отдать ---
        if (file_exists($cacheFull)) {
            $cacheMTime = filemtime($cacheFull);
            $sourceMTime = filemtime($sourceFull);

            $isFresh = $cacheMTime >= $sourceMTime;

            if ($isFresh && $cacheTtlDays !== null) {
                $ttlSeconds = $cacheTtlDays * 86400;
                if (time() - $cacheMTime > $ttlSeconds) {
                    $isFresh = false;
                }
            }

            if ($isFresh) {
                return $this->serve($cacheFull, $targetFormat);
            }
        }

        // --- 6. Генерация нового изображения ---
        // --- 6. Генерация нового изображения ---
        $image = $this->manager->read($sourceFull);

        $width = $presetConfig['width'] ?? null;
        $height = $presetConfig['height'] ?? null;
        $fit = $presetConfig['fit'] ?? 'contain';
        $upscale = $presetConfig['upscale'] ?? true; // по умолчанию апскейлим, если не указано иное

        // Размеры исходника
        $srcW = $image->width();
        $srcH = $image->height();

        if ($width && $height) {
            $isSmaller = $srcW < $width || $srcH < $height;

            if (! $upscale && $isSmaller) {
                // 🧸 Исходник меньше пресета и мы НЕ хотим его растягивать

                // 1. Вписываем исходник в рамку, но не увеличиваем (ratio <= 1)
                $ratio = min($width / $srcW, $height / $srcH, 1);
                $targetW = (int) round($srcW * $ratio);
                $targetH = (int) round($srcH * $ratio);

                if ($ratio < 1) {
                    $image = $image->resize($targetW, $targetH);
                }

                // 2. Создаём канвас нужного размера с белым фоном и кладём картинку по центру
                $canvas = $this->manager->create($width, $height);
                $canvas->fill('#ffffff');
                $canvas->place($image, 'center');

                $image = $canvas;
            } else {
                // Обычное поведение пресета
                if ($fit === 'crop') {
                    $image = $image->cover($width, $height);
                } elseif ($fit === 'contain') {
                    // Вписываем с сохранением пропорций, затем кладём на белый канвас
                    $ratio = min($width / $srcW, $height / $srcH);
                    $targetW = (int) round($srcW * $ratio);
                    $targetH = (int) round($srcH * $ratio);
                    $image = $image->resize($targetW, $targetH);

                    $canvas = $this->manager->create($width, $height);
                    $canvas->fill('#ffffff');
                    $canvas->place($image, 'center');

                    $image = $canvas;
                } else {
                    $image = $image->resize($width, $height);
                }
            }
        } elseif ($width || $height) {
            // Только одна сторона задана — тут апскейл обычно допустим
            $image = $image->resize($width, $height);
        }

        // --- 7. Подготовка директории и удаление старых кэшей ---
        $cacheDirFull = dirname($cacheFull);

        if (! is_dir($cacheDirFull)) {
            mkdir($cacheDirFull, 0755, true);
        }

        // Удаляем старые версии
        $pattern = $cacheDirFull.'/'.$filename.'-*.*';
        foreach (glob($pattern) as $oldFile) {
            @unlink($oldFile);
        }

        // --- 8. Сохраняем ---
        $image->encodeByExtension($targetFormat, quality: $quality)
            ->save($cacheFull);

        return $this->serve($cacheFull, $targetFormat);
    }

    private function parsePreset(string $preset): array
    {
        if (str_contains($preset, '.')) {
            return explode('.', $preset, 2);
        }

        return ['default', $preset];
    }

    private function serve(string $path, string $format): Response
    {
        $mime = match ($format) {
            'webp' => 'image/webp',
            'jpg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'image/'.$format,
        };

        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
