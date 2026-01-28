<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class AttributeValue extends Model
{
    use HasSlug;

    protected $table = 'attribute_values';

    protected $fillable = [
        'value',
        'sort',
        'attribute_id',
        'feature',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Товары, у которых установлено это значение атрибута.
     * pivot: product_attributes (product_id, attribute_value_id)
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_attributes',
            'attribute_value_id',
            'product_id'
        );
    }

    protected static function booted(): void
    {
        static::saved(function (AttributeValue $value) {
            $attribute = $value->attribute;

            if (! $attribute) {
                return;
            }

            // базовый slug атрибута
            $baseSlug = $attribute->getSlug() ?? Str::slug($attribute->name);

            // если это флажковый атрибут (Да/Нет/Опция)
            if ((int) $attribute->type === 1) {
                $map = [
                    'есть' => 'da',
                    'нет' => 'net',
                    'опция' => 'opciya',
                ];

                $key = mb_strtolower(trim($value->value));
                $suffix = $map[$key] ?? Str::slug($value->value);
                $slug = $baseSlug.'-'.$suffix;
            } else {
                // обычный тип — атрибут + значение
                // напр. "dalnost-2000-m"
                $slug = $baseSlug.'-'.Str::slug($value->value);
            }

            $value->setSlug($slug);
        });
    }
}
