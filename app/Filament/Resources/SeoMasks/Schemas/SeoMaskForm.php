<?php

namespace App\Filament\Resources\SeoMasks\Schemas;

use App\Models\Category;
use App\Models\CategoryLanding;
use App\Models\Product;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeoMaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Контекст и привязка')
                    ->description('Укажите контекст применения маски и связанную сущность')
                    ->schema([
                        TextInput::make('context')
                            ->label('Контекст')
                            ->required()
                            ->helperText('Например: category_page, product_page')
                            ->placeholder('category_page'),

                        MorphToSelect::make('maskable')
                            ->label('Связанная сущность')
                            ->types([
                                MorphToSelect\Type::make(Product::class)
                                    ->titleAttribute('name')
                                    ->label('Товар'),
                                MorphToSelect\Type::make(Category::class)
                                    ->titleAttribute('name')
                                    ->label('Категория'),
                                MorphToSelect\Type::make(CategoryLanding::class)
                                    ->titleAttribute('value')
                                    ->label('Посадочная страница'),
                            ])
                            ->searchable(),
                    ])
                    ->columns(1),

                Section::make('Шаблоны SEO')
                    ->description('Переменные: Товар → {name}, {price}, {category}, {attr_ID}. Категория → {name}, {parent}, {category}, {filters}. Лендинг → {name}, {category}, {filters}')
                    ->schema([
                        TextInput::make('meta_title_pattern')
                            ->label('Шаблон Meta Title')
                            ->maxLength(255)
                            ->placeholder('Купить {name} по цене {price} руб | {category}')
                            ->helperText('Для атрибутов: {attr_5} где 5 - ID атрибута. Пример: {attr_5} для бренда')
                            ->columnSpanFull(),

                        TextInput::make('meta_h1_pattern')
                            ->label('Шаблон H1')
                            ->maxLength(255)
                            ->placeholder('{name} - {category}')
                            ->helperText('Используйте переменные из описания секции выше')
                            ->columnSpanFull(),

                        Textarea::make('meta_description_pattern')
                            ->label('Шаблон Meta Description')
                            ->rows(3)
                            ->placeholder('Купить {name} по выгодной цене {price} руб. Доставка по всей России.')
                            ->helperText('Рекомендуется до 160 символов. Все переменные из описания секции доступны')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Дополнительно')
                    ->schema([
                        TextInput::make('extra')
                            ->label('Дополнительные данные')
                            ->helperText('JSON с дополнительными настройками')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
