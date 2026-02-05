<?php

namespace App\Filament\Resources\SeoMetas\Schemas;

use App\Models\Category;
use App\Models\CategoryLanding;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeoMetaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Привязка')
                    ->description('К какой сущности относятся эти SEO метатеги')
                    ->schema([
                        MorphToSelect::make('metaable')
                            ->label('Связанный объект')
                            ->types([
                                MorphToSelect\Type::make(Product::class)
                                    ->titleAttribute('name')
                                    ->label('Товар'),
                                MorphToSelect\Type::make(Category::class)
                                    ->titleAttribute('name')
                                    ->label('Категория'),
                                MorphToSelect\Type::make(CategoryLanding::class)
                                    ->titleAttribute('value')
                                    ->label('Посадочная страница'),
                            ])
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Основные метатеги')
                    ->description('Title, Description, Keywords и Canonical URL')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(60)
                            ->helperText('Рекомендуется до 60 символов')
                            ->columnSpanFull(),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Рекомендуется до 160 символов')
                            ->columnSpanFull(),

                        Textarea::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->rows(2)
                            ->helperText('Ключевые слова через запятую')
                            ->columnSpanFull(),

                        TextInput::make('canonical')
                            ->label('Canonical URL')
                            ->url()
                            ->helperText('Канонический адрес страницы')
                            ->columnSpanFull(),

                        Toggle::make('noindex')
                            ->label('NoIndex')
                            ->helperText('Запретить индексацию страницы поисковыми системами')
                            ->default(false),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Open Graph (Facebook, VK)')
                    ->description('Метатеги для социальных сетей')
                    ->schema([
                        TextInput::make('og_title')
                            ->label('OG Title')
                            ->maxLength(95)
                            ->helperText('Заголовок для соцсетей'),

                        TextInput::make('og_type')
                            ->label('OG Type')
                            ->default('website')
                            ->helperText('Обычно: website или article'),

                        Textarea::make('og_description')
                            ->label('OG Description')
                            ->rows(2)
                            ->maxLength(200)
                            ->columnSpanFull(),

                        FileUpload::make('og_image')
                            ->label('OG Image')
                            ->image()
                            ->imageEditor()
                            ->directory('seo/og')
                            ->maxSize(2048)
                            ->helperText('Рекомендуемый размер: 1200x630px')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Twitter Card')
                    ->description('Метатеги для Twitter/X')
                    ->schema([
                        TextInput::make('twitter_card')
                            ->label('Twitter Card Type')
                            ->default('summary_large_image')
                            ->helperText('summary или summary_large_image'),

                        TextInput::make('twitter_title')
                            ->label('Twitter Title')
                            ->maxLength(70)
                            ->helperText('Заголовок для Twitter'),

                        Textarea::make('twitter_description')
                            ->label('Twitter Description')
                            ->rows(2)
                            ->maxLength(200)
                            ->columnSpanFull(),

                        FileUpload::make('twitter_image')
                            ->label('Twitter Image')
                            ->image()
                            ->imageEditor()
                            ->directory('seo/twitter')
                            ->maxSize(2048)
                            ->helperText('Рекомендуемый размер: 1200x675px')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Дополнительно')
                    ->schema([
                        TextInput::make('extra')
                            ->label('Дополнительные метатеги')
                            ->helperText('Другие метатеги в формате JSON')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
