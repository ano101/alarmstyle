<?php

namespace App\Models;

use App\Models\Presenters\ProductPresenter;
use App\Models\Traits\HasSeo;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use SoftDeletes, HasSlug, Searchable, HasSeo;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'image',
        'popular',
        'description',
    ];

    protected $casts = [
        'popular' => 'integer',
    ];

    public function shouldBeSearchable(): bool
    {
        return is_null($this->deleted_at);
    }

    /**
     * При массовом импорте (scout:import) индексируем только живые товары.
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function searchableAs(): string
    {
        return 'products';
    }

    public function toSearchableArray(): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'image' => $this->image,
            'category_ids'        => $this->categories->pluck('id')->values()->all(),
            'main_category_id'    => $this->mainCategory()->first()?->id,
            'price'               => (double)optional($this->basePrice)->price,
            'brand'             => $this->attributeValues?->firstWhere('attribute_id', 58)?->value ?? '',
            'gps' => $this->attributeValues?->firstWhere('attribute_id', 56)?->value !== 'Нет',
            'gsm' => $this->attributeValues?->firstWhere('attribute_id', 55)?->value !== 'Нет',
            'auto' => $this->attributeValues?->firstWhere('attribute_id', 31)?->value !== 'Нет',
            // массив ID выбранных значений атрибутов
            'attribute_value_ids' => $this->attributeValues->pluck('id')->values()->all(),
            'popular'             => (int) $this->popular,
            'slug' => $this->getSlug()
        ];
    }

    public function images(): HasMany|Product
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Все цены товара
     */
    public function prices(): HasMany|Product
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Цена "просто" (например, базовая)
     */
    public function basePrice(): HasOne
    {
        return $this->hasOne(ProductPrice::class)
            ->where('type', ProductPrice::TYPE_BASE);
    }

    /**
     * Цена с установкой
     */
    public function priceWithInstall(): HasOne
    {
        return $this->hasOne(ProductPrice::class)
            ->where('type', ProductPrice::TYPE_WITH_INSTALL);
    }

    /**
     * Цена без установки
     */
    public function priceWithoutInstall(): HasOne
    {
        return $this->hasOne(ProductPrice::class)
            ->where('type', ProductPrice::TYPE_WITHOUT_INSTALL);
    }

    public function attributeValues():BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attributes');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)
            ->withPivot(['is_main'])
            ->withTimestamps();
    }

    /**
     * Получить основную категорию товара (отношение)
     */
    public function mainCategory()
    {
        return $this->belongsToMany(Category::class)
            ->wherePivot('is_main', true)
            ->withPivot(['is_main'])
            ->withTimestamps()
            ->limit(1);
    }

    /**
     * Аксессор для удобного доступа к главной категории как к свойству.
     * Использование: $product->main_category или $product->mainCategory
     */
    public function getMainCategoryAttribute(): ?Category
    {
        // Если отношение уже загружено через eager loading
        if ($this->relationLoaded('mainCategory')) {
            return $this->getRelation('mainCategory')->first();
        }

        // Иначе выполняем запрос
        return $this->mainCategory()->first();
    }

    /**
     * Задать главную категорию товара.
     *
     * @param  \App\Models\Category|int  $category
     */
    public function setMainCategory(Category|int $category): void
    {
        $categoryId = $category instanceof Category ? $category->id : $category;

        DB::transaction(function () use ($categoryId) {
            // Сбрасываем флаг у всех категорий товара
            DB::table('category_product')
                ->where('product_id', $this->id)
                ->update(['is_main' => false]);

            // Если связи ещё нет — attach, если есть — просто обновляем pivot
            if (! $this->categories()->where('category_id', $categoryId)->exists()) {
                $this->categories()->attach($categoryId, ['is_main' => true]);
            } else {
                $this->categories()->updateExistingPivot($categoryId, ['is_main' => true]);
            }
        });

        // Чтобы в уже загруженных отношениях тоже всё обновилось
        $this->unsetRelation('categories');
    }

    public function presenter(): ProductPresenter
    {
        return new ProductPresenter($this);
    }
}
