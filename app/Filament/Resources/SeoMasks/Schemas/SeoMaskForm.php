<?php

namespace App\Filament\Resources\SeoMasks\Schemas;

use App\Models\Category;
use App\Models\CategoryLanding;
use App\Models\Product;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SeoMaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('context')
                    ->required(),
                MorphToSelect::make('maskable')
                    ->types([
                        MorphToSelect\Type::make(Product::class)
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(Category::class)
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(CategoryLanding::class)
                            ->titleAttribute('value'),
                    ]),
                TextInput::make('meta_title_pattern'),
                TextInput::make('meta_h1_pattern'),
                Textarea::make('meta_description_pattern')
                    ->columnSpanFull(),
                TextInput::make('extra'),
            ]);
    }
}
