<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Настройка приложения')
                    ->description('Создайте параметр конфигурации для использования в приложении')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Ключ настройки')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Например: contacts.phone, contacts.whatsapp, social.telegram')
                            ->placeholder('contacts.phone'),

                        Forms\Components\Textarea::make('value')
                            ->label('Значение')
                            ->rows(3)
                            ->helperText('Простое строковое значение: телефон, адрес, ссылка и т.п.')
                            ->placeholder('+7 (999) 123-45-67')
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('data')
                            ->label('Дополнительные данные (JSON)')
                            ->helperText('Используйте для сложных структурированных данных')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }
}
