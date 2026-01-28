<?php

namespace App\Support\Blocks\Definitions;

use App\Support\Blocks\Concerns\HasWidth;
use App\Support\Blocks\Contracts\BlockDefinition;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class FeaturesBlock implements BlockDefinition
{
    use HasWidth;

    public static function make(): Block
    {
        return Block::make(self::getType())
            ->label('Блок преимуществ')
            ->schema([
                self::widthField(),

                TextInput::make('title')
                    ->label('Заголовок')
                    ->default('Почему выбирают нас')
                    ->required(),

                Textarea::make('description')
                    ->label('Описание')
                    ->default('Мы предлагаем профессиональный подход к защите вашего автомобиля')
                    ->rows(2)
                    ->nullable(),

                Repeater::make('items')
                    ->label('Преимущества')
                    ->schema([
                        Select::make('icon')
                            ->label('Иконка')
                            ->options([
                                'message-square' => 'Сообщение',
                                'award' => 'Награда',
                                'clock' => 'Часы',
                                'map-pin' => 'Метка на карте',
                                'shield' => 'Щит',
                                'check-circle' => 'Галочка в круге',
                                'star' => 'Звезда',
                                'zap' => 'Молния',
                            ])
                            ->default('message-square')
                            ->required(),

                        TextInput::make('title')
                            ->label('Название')
                            ->required(),

                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(2)
                            ->required(),
                    ])
                    ->defaultItems(4)
                    ->minItems(1)
                    ->maxItems(8)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }

    public static function getType(): string
    {
        return 'features';
    }
}
