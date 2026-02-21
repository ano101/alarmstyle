<?php

namespace App\Models;

use App\Models\Traits\HasSeo;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasSeo, HasSlug;

    protected $fillable = [
        'name',
        'image',
        'parent_id',
        'position',
        'is_active',
        'description',
    ];

    protected static function booted(): void
    {
        // после create/update
        static::saved(function (Category $category) {
            $baseSlug = $category->getSlug() ?? Str::slug($category->name);
            $category->setSlug($baseSlug);
        });
    }

    /**
     * Родительская категория
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Прямые дочерние категории
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Дочерние категории рекурсивно (для дерева)
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Scope для корневых категорий
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['is_main'])
            ->withTimestamps();
    }

    public function quickLinks(): MorphMany
    {
        return $this->morphMany(\App\Models\CatalogQuickLink::class, 'linkable')
            ->orderBy('sort');
    }

    public function getUrlAttribute(): ?string
    {
        return $this->slug ? '/category/'.$this->slug->slug : null;
    }
}
