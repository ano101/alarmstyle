<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Models\Menu;
use App\Models\MenuItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->description('Выберите меню и укажите название пункта')
                    ->schema([
                        Select::make('menu_id')
                            ->label('Меню')
                            ->required()
                            ->options(Menu::query()->pluck('name', 'id'))
                            ->searchable()
                            ->helperText('В какое меню добавить этот пункт'),

                        Select::make('parent_id')
                            ->label('Родительский пункт')
                            ->options(fn ($get) => MenuItem::query()
                                ->where('menu_id', $get('menu_id'))
                                ->pluck('label', 'id')
                            )
                            ->searchable()
                            ->nullable()
                            ->helperText('Оставьте пустым для пункта верхнего уровня'),

                        TextInput::make('label')
                            ->label('Название пункта')
                            ->required()
                            ->maxLength(120)
                            ->placeholder('О компании'),

                        TextInput::make('icon')
                            ->label('Иконка')
                            ->maxLength(80)
                            ->helperText('Например: heroicon-o-home')
                            ->placeholder('heroicon-o-home'),
                    ])
                    ->columns(2),

                Section::make('Ссылка')
                    ->description('Укажите тип и адрес ссылки')
                    ->schema([
                        Select::make('type')
                            ->label('Тип ссылки')
                            ->required()
                            ->options([
                                'group' => 'Группа (без ссылки)',
                                'internal_path' => 'Внутренняя ссылка (path)',
                                'external_url' => 'Внешняя ссылка (URL)',
                            ])
                            ->native(false)
                            ->live()
                            ->helperText('Группа используется для выпадающих меню'),

                        TextInput::make('path')
                            ->label('Внутренний путь')
                            ->visible(fn ($get) => $get('type') === 'internal_path')
                            ->required(fn ($get) => $get('type') === 'internal_path')
                            ->maxLength(2048)
                            ->placeholder('/about')
                            ->helperText('Например: /about, /catalog'),

                        TextInput::make('url')
                            ->label('Внешняя ссылка')
                            ->visible(fn ($get) => $get('type') === 'external_url')
                            ->required(fn ($get) => $get('type') === 'external_url')
                            ->url()
                            ->maxLength(2048)
                            ->placeholder('https://example.com')
                            ->helperText('Полный URL с протоколом'),
                    ])
                    ->columns(1),

                Section::make('Настройки отображения')
                    ->description('Порядок и статус пункта меню')
                    ->schema([
                        TextInput::make('sort')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0)
                            ->helperText('Чем меньше значение, тем выше в списке'),

                        Toggle::make('is_active')
                            ->label('Активен')
                            ->helperText('Неактивные пункты не отображаются в меню')
                            ->default(true),

                        Toggle::make('open_in_new_tab')
                            ->label('Открывать в новой вкладке')
                            ->helperText('Полезно для внешних ссылок')
                            ->default(false),
                    ])
                    ->columns(3),
            ]);
    }
}
