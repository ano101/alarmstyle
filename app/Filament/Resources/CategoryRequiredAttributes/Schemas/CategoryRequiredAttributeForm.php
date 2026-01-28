<?php

namespace App\Filament\Resources\CategoryRequiredAttributes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryRequiredAttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Обязательный атрибут для категории')
                    ->description('Связка: категория → обязательный атрибут.')
                    ->schema([
                        Select::make('category_id')
                            ->label('Категория')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->required(),

                        Select::make('attribute_id')
                            ->label('Атрибут')
                            ->relationship('attribute', 'name')
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(1),
            ]);
    }
}
