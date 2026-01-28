<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('key')
                    ->label('Ключ')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Например: contacts.phone, contacts.whatsapp'),

                Forms\Components\Textarea::make('value')
                    ->label('Значение')
                    ->rows(2)
                    ->helperText('Строковое значение: телефон, адрес, ссылка и т.п.'),

                Forms\Components\KeyValue::make('data')
                    ->label('Data (JSON)')
                    ->helperText('Используй для сложных структур, если понадобится.')
                    ->nullable(),
            ]);
    }
}
