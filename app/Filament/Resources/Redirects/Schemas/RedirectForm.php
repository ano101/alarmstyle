<?php

namespace App\Filament\Resources\Redirects\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RedirectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Настройки редиректа')
                    ->description('Укажите URL-адреса и параметры редиректа')
                    ->schema([
                        Forms\Components\TextInput::make('from_url')
                            ->label('Откуда (URL)')
                            ->placeholder('/old-page')
                            ->helperText('Введите путь, с которого нужно сделать редирект (например: /old-page)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('to_url')
                            ->label('Куда (URL)')
                            ->placeholder('/new-page')
                            ->helperText('Введите путь или полный URL, на который нужно редиректить (например: /new-page или https://example.com)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('status_code')
                            ->label('Код ответа')
                            ->options([
                                301 => '301 - Постоянный редирект',
                                302 => '302 - Временный редирект',
                            ])
                            ->default(301)
                            ->required()
                            ->native(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(1),
            ]);
    }
}
