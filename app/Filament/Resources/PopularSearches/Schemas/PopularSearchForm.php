<?php

namespace App\Filament\Resources\PopularSearches\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PopularSearchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Популярный поисковый запрос')
                    ->description('Добавьте часто используемый поисковый запрос для отображения на сайте')
                    ->schema([
                        TextInput::make('query')
                            ->label('Поисковый запрос')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Автоматические шлагбаумы')
                            ->helperText('Текст запроса, который будет отображаться пользователям'),

                        TextInput::make('position')
                            ->label('Позиция')
                            ->numeric()
                            ->default(0)
                            ->helperText('Порядок отображения (меньше = выше)'),

                        Toggle::make('is_active')
                            ->label('Активен')
                            ->helperText('Неактивные запросы не отображаются на сайте')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
