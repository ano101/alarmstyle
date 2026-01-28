<?php

namespace App\Filament\Resources\CategoryRequiredAttributes;

use App\Filament\Resources\CategoryRequiredAttributes\Pages\CreateCategoryRequiredAttribute;
use App\Filament\Resources\CategoryRequiredAttributes\Pages\EditCategoryRequiredAttribute;
use App\Filament\Resources\CategoryRequiredAttributes\Pages\ListCategoryRequiredAttributes;
use App\Filament\Resources\CategoryRequiredAttributes\Schemas\CategoryRequiredAttributeForm;
use App\Filament\Resources\CategoryRequiredAttributes\Tables\CategoryRequiredAttributesTable;
use App\Models\CategoryRequiredAttribute;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CategoryRequiredAttributeResource extends Resource
{
    protected static ?string $model = CategoryRequiredAttribute::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return CategoryRequiredAttributeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryRequiredAttributesTable::configure($table);
    }

    public static function getModelLabel(): string
    {
        return 'Обязательный атрибут категории';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Обязательные атрибуты категорий';
    }

    public static function getNavigationLabel(): string
    {
        return 'Обязательные атрибуты';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Каталог';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategoryRequiredAttributes::route('/'),
            'create' => CreateCategoryRequiredAttribute::route('/create'),
            'edit' => EditCategoryRequiredAttribute::route('/{record}/edit'),
        ];
    }
}
