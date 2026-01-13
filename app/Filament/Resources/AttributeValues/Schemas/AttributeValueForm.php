<?php

namespace App\Filament\Resources\AttributeValues\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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


                    ])
                    ->columns(2),
            ]);
    }
}
