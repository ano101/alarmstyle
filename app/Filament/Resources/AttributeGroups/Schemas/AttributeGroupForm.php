<?php

namespace App\Filament\Resources\AttributeGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttributeGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->description('Заполните данные группы атрибутов.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Название группы')
                            ->placeholder('Например: Основные характеристики')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Это название будет отображаться в админке и на сайте.'),
                    ])
                    ->columns(1),
            ]);
    }
}
