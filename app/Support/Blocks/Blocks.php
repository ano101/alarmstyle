<?php

namespace App\Support\Blocks;

use App\Support\Blocks\Definitions\AboutBlock;
use App\Support\Blocks\Definitions\ContactBlock;
use App\Support\Blocks\Definitions\FeaturesBlock;
use App\Support\Blocks\Definitions\HeadingBlock;
use App\Support\Blocks\Definitions\HeroBlock;
use App\Support\Blocks\Definitions\ImageBlock;
use App\Support\Blocks\Definitions\OurServicesBlock;
use App\Support\Blocks\Definitions\ProductsSliderBlock;
use App\Support\Blocks\Definitions\TextBlock;
use Filament\Forms\Components\Builder;

class Blocks
{
    /**
     * @var array<class-string>
     */
    protected static array $blocks = [
        HeadingBlock::class,
        TextBlock::class,
        ImageBlock::class,
        ProductsSliderBlock::class,
        HeroBlock::class,
        OurServicesBlock::class,
        FeaturesBlock::class,
        ContactBlock::class,
        AboutBlock::class,
    ];

    public static function builder(string $field = 'blocks'): Builder
    {
        return Builder::make($field)
            ->label('Блоки')
            ->collapsible()
            ->blocks(self::definitions());
    }

    public static function definitions(): array
    {
        return array_map(
            fn ($blockClass) => $blockClass::make(),
            self::$blocks
        );
    }

    /**
     * Регистрация кастомного блока
     */
    public static function register(string $blockClass): void
    {
        if (! in_array($blockClass, self::$blocks, true)) {
            self::$blocks[] = $blockClass;
        }
    }

    /**
     * Получить все зарегистрированные блоки
     */
    public static function getRegistered(): array
    {
        return self::$blocks;
    }
}
