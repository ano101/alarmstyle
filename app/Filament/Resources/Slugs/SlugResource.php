<?php

namespace App\Filament\Resources\Slugs;

use App\Filament\Resources\Slugs\Pages\CreateSlug;
use App\Filament\Resources\Slugs\Pages\EditSlug;
use App\Filament\Resources\Slugs\Pages\ListSlugs;
use App\Filament\Resources\Slugs\Schemas\SlugForm;
use App\Filament\Resources\Slugs\Tables\SlugsTable;
use App\Models\Slug;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SlugResource extends Resource
{
    protected static ?string $model = Slug::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Slug';

    public static function form(Schema $schema): Schema
    {
        return SlugForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SlugsTable::configure($table);
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
            'index' => ListSlugs::route('/'),
            'create' => CreateSlug::route('/create'),
            'edit' => EditSlug::route('/{record}/edit'),
        ];
    }
}
