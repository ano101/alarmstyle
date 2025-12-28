<?php

namespace App\Models\Traits;

trait HasBlocks
{
    public function initializeHasBlocks(): void
    {
        // Laravel сам будет json <-> array
        $this->casts['blocks'] = 'array';
    }

    public function getBlocksAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return json_decode($value, true) ?? [];
        }

        return [];
    }

    public function setBlocksAttribute($value): void
    {
        // Нормализуем: хотим всегда массив
        if (is_string($value)) {
            $value = json_decode($value, true) ?? [];
        }

        $this->attributes['blocks'] = json_encode($value ?? []);
    }

    /**
     * Блоки готовые к рендеру (можно будет обрабатывать хелпером тут).
     */
    public function getBlocksForRender(): array
    {
        return $this->blocks ?? [];
    }
}
