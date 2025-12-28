<?php

namespace App\Models;

use App\Models\Traits\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CategoryLanding extends Model
{
    use HasSeo;

    protected $table = 'category_landings';

    protected $fillable = [
        'category_id',
        'name',
        'attribute_value_ids',
        'content',
    ];

    protected $casts = [
        'attribute_value_ids' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function (CategoryLanding $landing) {
            $ids = $landing->getNormalizedAttributeValueIds();
            $landing->attribute_value_ids_key = implode(',', $ids);
        });
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Нормализованный массив ID значений атрибутов для сопоставления.
     */
    public function getNormalizedAttributeValueIds(): array
    {
        $ids = $this->attribute_value_ids ?? [];

        return collect($ids)
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();
    }

    public function quickLinks(): MorphMany
    {
        return $this->morphMany(\App\Models\CatalogQuickLink::class, 'linkable')
            ->orderBy('sort');
    }
}
