<?php

namespace App\Support\Blocks;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use App\Models\Category;

class Blocks
{
    public static function builder(string $field = 'blocks'): Builder
    {
        return Builder::make($field)
            ->label('Блоки')
            ->collapsible()
            ->blocks(self::definitions());
    }

    public static function definitions(): array
    {
        return [
            Block::make('heading')
                ->label('Заголовок')
                ->schema([
                    TextInput::make('text')
                        ->label('Текст')
                        ->required(),
                    TextInput::make('level')
                        ->label('Уровень (h1–h4)')
                        ->default('h2'),
                ]),

            Block::make('text')
                ->label('Текстовый блок')
                ->schema([
                    Textarea::make('content')
                        ->label('Текст')
                        ->rows(4)
                        ->required(),
                ]),

            Block::make('image')
                ->label('Картинка')
                ->schema([
                    FileUpload::make('url')
                        ->label('Картинка')
                        ->image()
                        ->required(),
                    TextInput::make('alt')
                        ->label('Alt')
                        ->nullable(),
                ]),

            Block::make('products_slider')
                ->label('Слайдер товаров')
                ->schema([
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
                            'popular'   => 'Популярные',
                            'new'       => 'Новинки',
                            'price_asc' => 'Цена ↑',
                            'price_desc'=> 'Цена ↓',
                        ])
                        ->default('popular'),

                    TextInput::make('limit')
                        ->label('Сколько товаров')
                        ->numeric()
                        ->default(10),
                ]),
        ];
    }
}
