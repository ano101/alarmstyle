<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasSlug;

    protected $table = 'attributes';

    protected $fillable = [
        'name',
        'type',
        'sort',
        'attribute_group_id',
        'in_filter',
        'type_front',
        'helper_text',
    ];

    protected $casts = [
        'in_filter' => 'boolean',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function attributeGroup(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class);
    }

    protected static function booted(): void
    {
        // после create/update
        static::saved(function (Attribute $attribute) {
            // 1) сам slug для атрибута
            $attribute->setSlug(Str::slug($attribute->name));

            // 2) для типа 1 — если значений ещё нет, создаём Есть/Нет/Опция
            if ((int) $attribute->type === 1 && $attribute->values()->count() === 0) {
                $defaults = [
                    'Есть' => 'est',
                    'Нет' => 'net',
                    'Опция' => 'opciya',
                ];

                foreach ($defaults as $label => $suffix) {
                    $value = $attribute->values()->create([
                        'value' => $label,
                    ]);

                    // base slug атрибута
                    $baseSlug = $attribute->getSlug() ?? Str::slug($attribute->name);
                    $value->setSlug($baseSlug.'-'.$suffix);
                }
            }
        });
    }
}
