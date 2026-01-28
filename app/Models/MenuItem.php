<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'type',
        'path',
        'url',
        'icon',
        'sort',
        'is_active',
        'open_in_new_tab',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function getResolvedHrefAttribute(): ?string
    {
        return match ($this->type) {
            'group' => null, // вкладка, не ссылка
            'external_url' => $this->url ?: null,
            'internal_path' => $this->path
                ? (str_starts_with($this->path, '/') ? $this->path : '/'.$this->path)
                : null,
            default => null,
        };
    }
}
