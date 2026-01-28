<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Support\Blocks\Blocks as BlocksBuilder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основное')
                    ->description('Заголовок, адрес страницы и статус публикации.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Заголовок страницы')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // если slug уже введён вручную — не трогаем
                                if ($get('slug.slug')) {
                                    return;
                                }

                                $slug = str($state)->slug()->value();

                                // чтобы главная страница могла быть пустой — slug "" = "/"
                                $set('slug.slug', $slug);
                            }),

                        TextInput::make('slug.slug')
                            ->label('Slug (URL)')
                            ->nullable()
                            ->maxLength(255)
                            ->unique(
                                table: 'slugs',
                                column: 'slug',
                                ignoreRecord: true, // <-- важно! не ругается на свой собственный slug
                            )
                            ->helperText('Оставь пустым для главной страницы "/"'),

                        Toggle::make('is_published')
                            ->label('Опубликовано')
                            ->helperText('Если выключено — страница будет недоступна по прямой ссылке.')
                            ->default(true)
                            ->columnSpan(1),

                        Toggle::make('is_homepage')
                            ->label('Главная страница')
                            ->helperText('Главная страница доступна по адресу "/"')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Если установлена как главная, очищаем слаг
                                    $set('slug.slug', '');
                                }
                            })
                            ->default(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2)->columnSpanFull(),

                Section::make('Контент страницы')
                    ->description('Собери страницу из блоков: текст, заголовки, баннеры, слайдеры товаров и т.д.')
                    ->schema([
                        BlocksBuilder::builder()
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
