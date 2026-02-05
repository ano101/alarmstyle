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
                            ->searchable()
                            ->helperText('Для какой сущности применяется маска'),
                    ])
                    ->columns(1),

                Section::make('Шаблоны SEO')
                    ->description('Используйте переменные в фигурных скобках, например: {name}, {category}')
                    ->schema([
                        TextInput::make('meta_title_pattern')
                            ->label('Шаблон Meta Title')
                            ->maxLength(255)
                            ->placeholder('Купить {name} в Москве | {category}')
                            ->helperText('Доступные переменные зависят от типа сущности')
                            ->columnSpanFull(),

                        TextInput::make('meta_h1_pattern')
                            ->label('Шаблон H1')
                            ->maxLength(255)
                            ->placeholder('{name} - {category}')
                            ->columnSpanFull(),

                        Textarea::make('meta_description_pattern')
                            ->label('Шаблон Meta Description')
                            ->rows(3)
                            ->placeholder('Купить {name} по выгодной цене. {description}. Доставка по всей России.')
                            ->helperText('Рекомендуемая длина до 160 символов')
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
