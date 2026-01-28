<?php

namespace App\Support\Blocks\Definitions;

use App\Support\Blocks\Concerns\HasWidth;
use App\Support\Blocks\Contracts\BlockDefinition;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class OurServicesBlock implements BlockDefinition
{
    use HasWidth;

    public static function make(): Block
    {
        return Block::make(self::getType())
            ->label('Наши услуги')
            ->schema([
                self::widthField(),

                TextInput::make('title')
                    ->label('Заголовок')
                    ->default('Наши услуги')
                    ->required(),

                Textarea::make('description')
                    ->label('Описание')
                    ->default('Профессиональная установка автомобильного оборудования с гарантией качества')
                    ->rows(2)
                    ->nullable(),

                Repeater::make('services')
                    ->label('Услуги')
                    ->schema([
                        Select::make('icon')
                            ->label('Иконка')
                            ->options([
                                'car' => 'Автомобиль',
                                'radio' => 'Радио',
                                'satellite' => 'Спутник',
                                'lock' => 'Замок',
                                'camera' => 'Камера',
                                'map-pin' => 'Метка на карте',
                                'shield' => 'Щит',
                                'key' => 'Ключ',
                                'monitor' => 'Монитор',
                                'speaker' => 'Динамик',
                            ])
                            ->default('car')
                            ->required(),

                        TextInput::make('title')
                            ->label('Название услуги')
                            ->required(),

                        TextInput::make('price')
                            ->label('Цена')
                            ->default('От 3000₽')
                            ->required(),

                        TagsInput::make('features')
                            ->label('Характеристики')
                            ->placeholder('Добавьте характеристику')
                            ->default(['Пример характеристики'])
                            ->required(),
                    ])
                    ->defaultItems(6)
                    ->minItems(1)
                    ->maxItems(12)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }

    public static function getType(): string
    {
        return 'our_services';
    }
}
