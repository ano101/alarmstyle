<?php

namespace App\Filament\Resources\Slugs\Schemas;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SlugForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                MorphToSelect::make('sluggable')
                    ->types([
                        MorphToSelect\Type::make(Product::class)
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(Category::class)
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(AttributeValue::class)
                            ->titleAttribute('value'),
                        MorphToSelect\Type::make(Attribute::class)
                            ->titleAttribute('name'),
                    ]),
                TextInput::make('slug')
                    ->required(),
            ]);
    }
}
