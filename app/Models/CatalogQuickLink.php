<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CatalogQuickLink extends Model
{
    protected $fillable = [
        'label', 'sort', 'is_active', 'color', 'icon',
        'type', 'path', 'url',
    ];

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function resolveHref(): string
    {
        // ВАЖНО: route('catalog', ['path' => ...]) у тебя требует непустой path
        // Поэтому для каталога используем slug категории.
        $category = null;

        if ($this->linkable instanceof \App\Models\Category) {
            $category = $this->linkable;
        } elseif ($this->linkable instanceof \App\Models\CategoryLanding) {
            $category = $this->linkable->category;
        }

        return match ($this->type) {
            'catalog_category' => route('catalog', ['path' => $category?->getSlug()]),
            'catalog_landing' => ($this->linkable instanceof \App\Models\CategoryLanding)
                ? $this->linkable->getCatalogUrl()
                : route('catalog', ['path' => $category?->getSlug()]),
            'catalog_path' => route('catalog', ['path' => trim((string) $this->path, '/')]),
            'custom_url' => (string) $this->url,
            default => route('catalog', ['path' => $category?->getSlug()]),
        };
    }
}
