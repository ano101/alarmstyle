<?php

namespace App\Models\Presenters;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Str;

class ProductPresenter
{
    public function __construct(
        protected Product $product
    ) {}

    /**
     * Кэш значений атрибутов: [attribute_id => value]
     */
    protected ?array $attrs = null;

    /**
     * Кэш маппинга атрибутов из настроек
     */
    protected ?array $attributeMapping = null;

    protected function attrs(): array
    {
        if ($this->attrs !== null) {
            return $this->attrs;
        }

        $this->attrs = $this->product->attributeValues
            ?->pluck('value', 'attribute_id')
            ?->all() ?? [];

        return $this->attrs;
    }

    protected function getAttributeMapping(): array
    {
        if ($this->attributeMapping !== null) {
            return $this->attributeMapping;
        }

        $this->attributeMapping = Setting::getData('product_attribute_mapping', [
            'brand' => 58,
            'gps' => 56,
            'gsm' => 55,
            'auto_start' => 31,
        ]);

        return $this->attributeMapping;
    }

    protected function getMappedAttributeId(string $key): ?int
    {
        return $this->getAttributeMapping()[$key] ?? null;
    }

    protected function attr(int $attributeId, mixed $default = ''): mixed
    {
        return $this->attrs()[$attributeId] ?? $default;
    }

    protected function attrYes(int $attributeId): bool
    {
        $value = $this->attr($attributeId, null);

        if ($value === null) {
            return false;
        }

        $normalized = Str::of((string) $value)->trim()->lower();

        // если у вас строго "Да/Нет" — этого достаточно
        return $normalized !== 'нет' && $normalized !== '' && $normalized !== '0';
    }

    public function brand(): string
    {
        $attributeId = $this->getMappedAttributeId('brand');

        return $attributeId ? (string) $this->attr($attributeId, '') : '';
    }

    public function gps(): bool
    {
        $attributeId = $this->getMappedAttributeId('gps');

        return $attributeId ? $this->attrYes($attributeId) : false;
    }

    public function gsm(): bool
    {
        $attributeId = $this->getMappedAttributeId('gsm');

        return $attributeId ? $this->attrYes($attributeId) : false;
    }

    public function autoStart(): bool
    {
        $attributeId = $this->getMappedAttributeId('auto_start');

        return $attributeId ? $this->attrYes($attributeId) : false;
    }

    public function formattedPrice(): string
    {
        $price = (float) ($this->product->basePrice->price ?? 0);

        return number_format($price, 2, ',', ' ').' ₽';
    }

    public function mainCategoryName(): string
    {
        return $this->product->mainCategory()->first()?->name ?? 'Без категории';
    }

    public function popularStatus(): string
    {
        return $this->product->popular ? 'Популярный' : 'Обычный';
    }

    public function imageUrl(): string
    {
        return $this->product->image
            ? asset('storage/'.$this->product->image)
            : asset('images/no-image.png');
    }
}
