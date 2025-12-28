<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Название')
                ->required()
                ->maxLength(100),

            TextInput::make('key')
                ->label('Ключ')
                ->helperText('Например: header, footer, mobile')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true),

            Toggle::make('is_active')
                ->label('Активно')
                ->default(true),
        ]);
    }
}
