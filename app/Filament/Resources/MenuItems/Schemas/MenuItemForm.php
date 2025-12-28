<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Models\Menu;
use App\Models\MenuItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('menu_id')
                    ->label('Меню')
                    ->required()
                    ->options(Menu::query()->pluck('name', 'id'))
                    ->searchable(),

                Select::make('parent_id')
                    ->label('Родитель')
                    ->options(fn ($get) =>
                    MenuItem::query()
                        ->where('menu_id', $get('menu_id'))
                        ->pluck('label', 'id')
                    )
                    ->searchable()
                    ->nullable()
                    ->helperText('Только пункты этого же меню'),

                TextInput::make('label')
                    ->label('Название')
                    ->required()
                    ->maxLength(120),

                Select::make('type')
                    ->label('Тип ссылки')
                    ->required()
                    ->options([
                        'group'         => 'Вкладка (без ссылки)',
                        'internal_path' => 'Внутренняя (path)',
                        'external_url'  => 'Внешняя (URL)',
                    ])
                    ->reactive(),

                TextInput::make('path')
                    ->label('Path')
                    ->visible(fn ($get) => $get('type') === 'internal_path')
                    ->required(fn ($get) => $get('type') === 'internal_path')
                    ->maxLength(2048),

                TextInput::make('url')
                    ->label('URL')
                    ->visible(fn ($get) => $get('type') === 'external_url')
                    ->required(fn ($get) => $get('type') === 'external_url')
                    ->url()
                    ->maxLength(2048),

                TextInput::make('icon')
                    ->label('Иконка')
                    ->maxLength(80)
                    ->helperText('Например: heroicon-o-home'),

                TextInput::make('sort')
                    ->label('Сортировка')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Активен')
                    ->default(true),

                Toggle::make('open_in_new_tab')
                    ->label('Открывать в новой вкладке')
                    ->default(false),
            ]);
    }
}
