<?php

namespace App\Filament\Resources\CategoryLandings\Schemas;

use App\Models\AttributeValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryLandingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Основная информация')
                    ->description('Категория, название и набор атрибутов для лендинга.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category_id')
                                    ->label('Категория')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->required(),

                                TextInput::make('name')
                                    ->label('Название лендинга')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Repeater::make('attribute_value_ids')
                            ->label('Атрибуты')
                            ->helperText('Выберите значения атрибутов, например: Цвет — Красный, Страна — Франция и т.п.')
                            ->schema([
                                Select::make('attribute_value_id')
                                    ->label('Значение атрибута')
                                    ->searchable()
                                    ->options(function () {
                                        return AttributeValue::query()
                                            ->with('attribute')
                                            ->get()
                                            ->mapWithKeys(function (AttributeValue $attributeValue) {
                                                $label = $attributeValue->attribute
                                                    ? "{$attributeValue->attribute->name} — {$attributeValue->value}"
                                                    : $attributeValue->value;

                                                return [
                                                    $attributeValue->id => $label,
                                                ];
                                            })
                                            ->toArray();
                                    })
                                    ->getSearchResultsUsing(function (string $search): array {
                                        return AttributeValue::query()
                                            ->with('attribute')
                                            ->where('value', 'like', "%{$search}%")
                                            ->orWhereHas('attribute', function ($q) use ($search) {
                                                $q->where('name', 'like', "%{$search}%");
                                            })
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(function (AttributeValue $attributeValue) {
                                                $label = $attributeValue->attribute
                                                    ? "{$attributeValue->attribute->name} — {$attributeValue->value}"
                                                    : $attributeValue->value;

                                                return [
                                                    $attributeValue->id => $label,
                                                ];
                                            })
                                            ->toArray();
                                    })
                                    ->getOptionLabelUsing(function ($value) {
                                        if (! $value) {
                                            return null;
                                        }

                                        $attributeValue = AttributeValue::query()
                                            ->with('attribute')
                                            ->find($value);

                                        if (! $attributeValue) {
                                            return null;
                                        }

                                        return $attributeValue->attribute
                                            ? "{$attributeValue->attribute->name} — {$attributeValue->value}"
                                            : $attributeValue->value;
                                    })
                                    ->required(),
                            ])
                            ->default([])
                            ->addActionLabel('Добавить атрибут')
                            ->columns(1)
                            ->minItems(1) // Минимум 1 атрибут обязателен
                            ->itemLabel(fn (array $state): ?string => $state['attribute_value_id']
                                    ? AttributeValue::find($state['attribute_value_id'])?->value
                                    : null
                            )

                            // При сохранении: превращаем массив объектов репитера в плоский массив ID
                            ->dehydrateStateUsing(function ($state): array {
                                // $state = [
                                //   ['attribute_value_id' => 1],
                                //   ['attribute_value_id' => 5],
                                // ]
                                return collect($state)
                                    ->pluck('attribute_value_id')
                                    ->filter()
                                    ->map(fn ($id) => (int) $id)
                                    ->values()
                                    ->all();
                            })

                            // При загрузке из БД: из плоского массива ID делаем массив объектов для репитера
                            ->afterStateHydrated(function (Repeater $component, $state): void {
                                // $state (из БД) = [1, 5, 23]
                                $component->state(
                                    collect($state ?? [])
                                        ->map(fn ($id) => ['attribute_value_id' => $id])
                                        ->toArray()
                                );
                            })

                            ->required(),
                    ])
                    ->columns(1),

                Section::make('Содержимое')
                    ->schema([
                        Textarea::make('content')
                            ->label('Текст лендинга')
                            ->rows(8)
                            ->autosize()
                            ->placeholder('Опишите здесь основной контент лендинга...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
