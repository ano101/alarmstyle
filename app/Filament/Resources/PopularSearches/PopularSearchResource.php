<?php

namespace App\Filament\Resources\PopularSearches;

use App\Filament\Resources\PopularSearches\Pages\CreatePopularSearch;
use App\Filament\Resources\PopularSearches\Pages\EditPopularSearch;
use App\Filament\Resources\PopularSearches\Pages\ListPopularSearches;
use App\Filament\Resources\PopularSearches\Schemas\PopularSearchForm;
use App\Filament\Resources\PopularSearches\Tables\PopularSearchesTable;
use App\Models\PopularSearch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PopularSearchResource extends Resource
{
    protected static ?string $model = PopularSearch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $navigationLabel = 'Популярные запросы';

    protected static ?string $modelLabel = 'популярный запрос';

    protected static ?string $pluralModelLabel = 'Популярные запросы';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Контент';
    }

    public static function form(Schema $schema): Schema
    {
        return PopularSearchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PopularSearchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPopularSearches::route('/'),
            'create' => CreatePopularSearch::route('/create'),
            'edit' => EditPopularSearch::route('/{record}/edit'),
        ];
    }
}
