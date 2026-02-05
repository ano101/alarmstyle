<?php

namespace App\Filament\Resources\Slugs\Schemas;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SlugForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('URL Slug')
                    ->description('Создайте или отредактируйте URL адрес для сущности')
                    ->schema([
                        MorphToSelect::make('sluggable')
                            ->label('Связанная сущность')
                            ->types([
                                MorphToSelect\Type::make(Product::class)
                                    ->titleAttribute('name')
                                    ->label('Товар'),
                                MorphToSelect\Type::make(Category::class)
                                    ->titleAttribute('name')
                                    ->label('Категория'),
                                MorphToSelect\Type::make(AttributeValue::class)
                                    ->titleAttribute('value')
                                    ->label('Значение атрибута'),
                                MorphToSelect\Type::make(Attribute::class)
                                    ->titleAttribute('name')
                                    ->label('Атрибут'),
                                MorphToSelect\Type::make(Page::class)
                                    ->titleAttribute('title')
                                    ->label('Страница'),
                            ])
                            ->searchable()
                            ->required()
                            ->helperText('Выберите объект, для которого создается slug')
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('o-kompanii')
                            ->helperText('Только латинские буквы, цифры и дефисы. Пример: tovary-dlya-doma')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }
}
