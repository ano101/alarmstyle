<?php

namespace App\Filament\Resources\SeoMetas\Schemas;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\CategoryLanding;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SeoMetaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                MorphToSelect::make('metaable')
                    ->types([
                        MorphToSelect\Type::make(Product::class)
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(Category::class)
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(CategoryLanding::class)
                            ->titleAttribute('value'),
                    ]),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
                Textarea::make('meta_keywords')
                    ->columnSpanFull(),
                TextInput::make('canonical'),
                TextInput::make('og_title'),
                Textarea::make('og_description')
                    ->columnSpanFull(),
                FileUpload::make('og_image')
                    ->image(),
                TextInput::make('og_type')
                    ->default('website'),
                TextInput::make('twitter_card')
                    ->default('summary_large_image'),
                TextInput::make('twitter_title'),
                Textarea::make('twitter_description')
                    ->columnSpanFull(),
                FileUpload::make('twitter_image')
                    ->image(),
                Toggle::make('noindex')
                    ->required(),
                TextInput::make('extra'),
            ]);
    }
}
