<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\CategoryRequiredAttribute;
use App\Models\Product;
use App\Models\ProductPrice;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProductForm
{


    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Общий блок: название + категории
                Section::make('Основная информация')
                    ->description('Заполните основные данные о товаре и его категорию.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Название товара')
                            ->placeholder('Например: Автоматический шлагбаум')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Select::make('categories')
                            ->required()
                            ->label('Категории')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Выберите одну или несколько категорий для товара.'),

                        Select::make('main_category_id')
                            ->label('Главная категория')
                            ->options(fn () => Category::pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required()
                            ->helperText('Эта категория будет считаться основной. Не забудьте добавить её выше в список категорий.')
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Set $set, ?Product $record) {
                                if (! $record) {
                                    return;
                                }

                                $mainCategory = $record->mainCategory();
                                if ($mainCategory) {
                                    $set('main_category_id', $mainCategory->id);
                                }
                            }),
                    ])
                    ->columns(2),

                // 2. Характеристики + цены в одном блоке
                Section::make('Характеристики и цены')
                    ->description('Значения атрибутов и цены товара.')
                    ->schema([
                        Select::make('attributeValues')
                            ->label('Характеристики (значения атрибутов)')
                            ->relationship('attributeValues', 'value')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Набор обязательных атрибутов зависит от выбранных категорий.')
                            ->getOptionLabelFromRecordUsing(fn (AttributeValue $record) => $record->attribute->name.': '.$record->value)
                            ->rules([
                                'array',
                                fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $categoryIds = $get('categories') ?? [];

                                    // все обязательные attribute_id для выбранных категорий
                                    $requiredAttributeIds = CategoryRequiredAttribute::query()
                                        ->whereIn('category_id', $categoryIds)
                                        ->pluck('attribute_id')
                                        ->unique();

                                    if ($requiredAttributeIds->isEmpty()) {
                                        return;
                                    }

                                    $selectedValueIds = is_array($value) ? $value : [];

                                    if (empty($selectedValueIds)) {
                                        $attrNames = Attribute::whereIn('id', $requiredAttributeIds)->pluck('name')->toArray();
                                        $fail('Обязательно укажите значения для атрибутов: ' . implode(', ', $attrNames) . '.');
                                        return;
                                    }

                                    // какие attribute_id покрыты выбранными значениями
                                    $selectedAttributeIds = AttributeValue::query()
                                        ->whereIn('id', $selectedValueIds)
                                        ->pluck('attribute_id')
                                        ->unique();

                                    $missingAttributeIds = $requiredAttributeIds->diff($selectedAttributeIds);

                                    if ($missingAttributeIds->isNotEmpty()) {
                                        $attrNames = Attribute::whereIn('id', $missingAttributeIds)->pluck('name')->toArray();
                                        $fail('Не выбраны значения для обязательных атрибутов: ' . implode(', ', $attrNames) . '.');
                                    }
                                },
                            ]),
                        Repeater::make('prices')
                            ->label('Цены')
                            ->relationship('prices')
                            ->schema([
                                Select::make('type')
                                    ->label('Тип цены')
                                    ->options([
                                        ProductPrice::TYPE_BASE            => 'Цена',
                                        ProductPrice::TYPE_WITH_INSTALL    => 'Цена с установкой',
                                        ProductPrice::TYPE_WITHOUT_INSTALL => 'Цена без установки',
                                    ])
                                    ->required()
                                    ->native(false),

                                TextInput::make('price')
                                    ->label('Сумма')
                                    ->numeric()
                                    ->required()
                                    ->helperText('Укажите цену в рублях (можно с копейками).'),
                            ])
                            ->addActionLabel('Добавить цену')
                            ->minItems(1)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                // 3. Медиа: основная картинка + галерея
                Section::make('Медиа')
                    ->description('Основное изображение и галерея товара.')
                    ->schema([
                        FileUpload::make('image')
                            ->disk('public')
                            ->label('Основное изображение')
                            ->image()
                            ->directory('images/products')
                            ->imagePreviewHeight('200')
                            ->openable()
                            ->downloadable(),

                        Repeater::make('images')
                            ->label('Галерея изображений')
                            ->relationship('images')
                            ->defaultItems(0)
                            ->schema([
                                FileUpload::make('url')
                                    ->disk('public')
                                    ->label('Изображение')
                                    ->image()
                                    ->directory('images/products')
                                    ->imagePreviewHeight('150')
                                    ->openable()
                                    ->downloadable(),
                            ])
                            ->addActionLabel('Добавить изображение'),
                    ])
                    ->columns(2),
            ])->columns(1);
    }
}
