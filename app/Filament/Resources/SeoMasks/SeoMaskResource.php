<?php

namespace App\Filament\Resources\SeoMasks;

use App\Filament\Resources\SeoMasks\Pages\CreateSeoMask;
use App\Filament\Resources\SeoMasks\Pages\EditSeoMask;
use App\Filament\Resources\SeoMasks\Pages\ListSeoMasks;
use App\Filament\Resources\SeoMasks\Schemas\SeoMaskForm;
use App\Filament\Resources\SeoMasks\Tables\SeoMasksTable;
use App\Models\SeoMask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SeoMaskResource extends Resource
{
    protected static ?string $model = SeoMask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SeoMaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeoMasksTable::configure($table);
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
            'index' => ListSeoMasks::route('/'),
            'create' => CreateSeoMask::route('/create'),
            'edit' => EditSeoMask::route('/{record}/edit'),
        ];
    }
}
