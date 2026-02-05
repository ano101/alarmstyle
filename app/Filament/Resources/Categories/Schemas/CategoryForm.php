<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->description('Название категории и её расположение в структуре')
                    ->schema([
                        Select::make('parent_id')
                            ->label('Родительская категория')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Оставьте пустым для категории верхнего уровня'),

                        TextInput::make('name')
                            ->label('Название категории')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('position')
                            ->label('Порядок сортировки')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Чем меньше число, тем выше в списке')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Изображение и статус')
                    ->description('Визуальное оформление категории')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Изображение категории')
                            ->image()
                            ->imageEditor()
                            ->directory('categories')
                            ->maxSize(2048)
                            ->helperText('Рекомендуемый размер: 800x600px'),

                        Toggle::make('is_active')
                            ->label('Активна')
                            ->helperText('Неактивные категории скрыты на сайте')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
