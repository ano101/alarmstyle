<?php

namespace App\Support\Blocks\Definitions;

use App\Support\Blocks\Concerns\HasWidth;
use App\Support\Blocks\Contracts\BlockDefinition;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class AboutBlock implements BlockDefinition
{
    use HasWidth;

    public static function make(): Block
    {
        return Block::make(self::getType())
            ->label('О компании')
            ->schema([
                self::widthField(),

                TextInput::make('company_name')
                    ->label('Название компании')
                    ->default('AlarmStyle')
                    ->required(),

                Textarea::make('description')
                    ->label('Описание компании')
                    ->rows(3)
                    ->default('Мы специализируемся на установке современных охранных систем для автомобилей. Наша миссия — обеспечить максимальную безопасность вашего транспорта с использованием передовых технологий и профессионального подхода.')
                    ->nullable(),

                Repeater::make('stats')
                    ->label('Статистика')
                    ->schema([
                        TextInput::make('value')
                            ->label('Значение')
                            ->required(),

                        TextInput::make('label')
                            ->label('Подпись')
                            ->required(),
                    ])
                    ->defaultItems(4)
                    ->minItems(2)
                    ->maxItems(8)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => ($state['value'] ?? '').' - '.($state['label'] ?? null)),

                Repeater::make('advantages')
                    ->label('Преимущества')
                    ->schema([
                        Select::make('icon')
                            ->label('Иконка')
                            ->options([
                                'Clock' => 'Часы',
                                'Users' => 'Пользователи',
                                'Shield' => 'Щит',
                                'Award' => 'Награда',
                                'Star' => 'Звезда',
                                'CheckCircle2' => 'Галочка',
                                'TrendingUp' => 'Рост',
                                'Heart' => 'Сердце',
                            ])
                            ->default('Award')
                            ->required(),

                        TextInput::make('title')
                            ->label('Заголовок')
                            ->required(),

                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(2)
                            ->required(),
                    ])
                    ->defaultItems(4)
                    ->minItems(2)
                    ->maxItems(8)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),

                FileUpload::make('about_image')
                    ->label('Изображение о компании')
                    ->image()
                    ->directory('images/about/company')
                    ->visibility('public')
                    ->nullable(),

                Repeater::make('licenses')
                    ->label('Лицензии и сертификаты')
                    ->schema([
                        TextInput::make('title')
                            ->label('Название')
                            ->required(),

                        FileUpload::make('image')
                            ->label('Изображение')
                            ->image()
                            ->directory('images/about/licenses')
                            ->visibility('public')
                            ->required(),

                        TextInput::make('number')
                            ->label('Номер')
                            ->required(),

                        TextInput::make('date')
                            ->label('Дата выдачи')
                            ->required(),
                    ])
                    ->defaultItems(4)
                    ->minItems(1)
                    ->maxItems(12)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),

                Repeater::make('team_list')
                    ->label('Преимущества команды')
                    ->simple(
                        TextInput::make('item')
                            ->label('Пункт')
                            ->required()
                    )
                    ->defaultItems(4)
                    ->minItems(1)
                    ->maxItems(12),

                FileUpload::make('team_image')
                    ->label('Изображение команды')
                    ->image()
                    ->directory('images/about/team')
                    ->visibility('public')
                    ->nullable(),
            ]);
    }

    public static function getType(): string
    {
        return 'about';
    }
}
