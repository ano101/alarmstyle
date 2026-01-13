<?php

namespace App\Filament\Resources\Attributes\Schemas;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->description('Заполните данные атрибута.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Название атрибута')
                            ->placeholder('Например: Цвет, Год выпуска')
                            ->required(),

                        Select::make('type')
                            ->label('Тип атрибута')
                            ->options([
                                1 => 'Да / Нет / Опция',
                                2 => 'Обычное значение',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('sort')
                            ->label('Сортировка')
                            ->numeric()
                            ->default(1)
                            ->required(),

                        Toggle::make('in_filter')->label('Включить в фильтр')->default(false),

                        Select::make('attribute_group_id')
                            ->label('Группа атрибутов')
                            ->searchable()
                            ->options(fn () => \App\Models\AttributeGroup::pluck('name', 'id')->toArray())
                            ->required(),
                        TextInput::make('helper_text')
                            ->label('Подсказка')
                            ->placeholder('Например: Используется на карточке товара'),
                    ])
                    ->columns(2),
            ]);
    }
}
