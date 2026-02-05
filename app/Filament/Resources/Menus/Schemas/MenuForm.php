<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Настройки меню')
                ->description('Создайте или отредактируйте меню для размещения на сайте')
                ->schema([
                    TextInput::make('name')
                        ->label('Название меню')
                        ->required()
                        ->maxLength(100)
                        ->helperText('Для внутреннего использования в админке')
                        ->placeholder('Главное меню'),

                    TextInput::make('key')
                        ->label('Системный ключ')
                        ->helperText('Используется в коде для вызова меню')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true)
                        ->placeholder('header'),

                    Toggle::make('is_active')
                        ->label('Активно')
                        ->helperText('Неактивные меню не отображаются на сайте')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
