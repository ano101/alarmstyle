<?php

namespace App\Filament\Resources\AttributeValues\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttributeValueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Значение атрибута')
                    ->description('Укажите значение для выбранного атрибута.')
                    ->schema([
                        Select::make('attribute_id')
                            ->label('Атрибут')
                            ->relationship('attribute', 'name')
                            ->required()
                            ->searchable(),

                        TextInput::make('value')
                            ->label('Значение')
                            ->placeholder('Например: Красный, 2024, Да')
                            ->required(),

                        Textarea::make('feature')
                            ->label('Основная особенность')
                            ->placeholder('Краткое описание ключевой функции (например: Интеграция с CAN-шиной автомобиля)')
                            ->helperText('Заполняется только для самых важных характеристик, которые будут отображаться в блоке "Основные особенности"')
                            ->rows(2)
                            ->columnSpanFull(),

                    ])
                    ->columns(2),
            ]);
    }
}
