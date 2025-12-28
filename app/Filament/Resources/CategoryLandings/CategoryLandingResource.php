<?php

namespace App\Filament\Resources\CategoryLandings;

use App\Filament\Resources\CategoryLandings\Pages\CreateCategoryLanding;
use App\Filament\Resources\CategoryLandings\Pages\EditCategoryLanding;
use App\Filament\Resources\CategoryLandings\Pages\ListCategoryLandings;
use App\Filament\Resources\CategoryLandings\RelationManagers\CatalogQuickLinkRelationManager;
use App\Filament\Resources\CategoryLandings\RelationManagers\SeoMetaRelationManager;
use App\Filament\Resources\CategoryLandings\Schemas\CategoryLandingForm;
use App\Filament\Resources\CategoryLandings\Tables\CategoryLandingsTable;
use App\Models\CategoryLanding;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CategoryLandingResource extends Resource
{
    protected static ?string $model = CategoryLanding::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CategoryLandingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryLandingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            'seoMeta' => SeoMetaRelationManager::class,
            'quickLinks' => CatalogQuickLinkRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategoryLandings::route('/'),
            'create' => CreateCategoryLanding::route('/create'),
            'edit' => EditCategoryLanding::route('/{record}/edit'),
        ];
    }
}
