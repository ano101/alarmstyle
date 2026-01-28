<?php

namespace App\Models\Presenters;

use App\Models\Product;
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
        return (string) $this->attr(58, '');
    }

    public function gps(): bool
    {
        return $this->attrYes(56);
    }

    public function gsm(): bool
    {
        return $this->attrYes(55);
    }

    public function autoStart(): bool
    {
        return $this->attrYes(31);
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
