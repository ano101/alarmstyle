<?php

namespace App\Support\Blocks\Definitions;

use App\Models\Category;
use App\Support\Blocks\Concerns\HasWidth;
use App\Support\Blocks\Contracts\BlockDefinition;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class ProductsSliderBlock implements BlockDefinition
{
    use HasWidth;

    public static function make(): Block
    {
        return Block::make(self::getType())
            ->label('Слайдер товаров')
            ->schema([
                self::widthField(),

                TextInput::make('title')
                    ->label('Заголовок блока')
                    ->default('Популярные товары'),

                Select::make('category_id')
                    ->label('Категория')
                    ->options(fn () => Category::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                    )
                    ->searchable()
                    ->nullable(),

                Select::make('sort')
                    ->label('Сортировка')
                    ->options([
                        'popular' => 'Популярные',
                        'new' => 'Новинки',
                        'price_asc' => 'Цена ↑',
                        'price_desc' => 'Цена ↓',
                    ])
                    ->default('popular'),

                TextInput::make('limit')
                    ->label('Сколько товаров')
                    ->numeric()
                    ->default(10),
            ]);
    }

    public static function getType(): string
    {
        return 'products_slider';
    }

    protected static function getDefaultWidth(): string
    {
        return 'full';
    }
}
