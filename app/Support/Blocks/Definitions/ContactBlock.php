<?php

namespace App\Support\Blocks\Definitions;

use App\Support\Blocks\Concerns\HasWidth;
use App\Support\Blocks\Contracts\BlockDefinition;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ContactBlock implements BlockDefinition
{
    use HasWidth;

    public static function make(): Block
    {
        return Block::make(self::getType())
            ->label('Страница контакты')
            ->schema([
                self::widthField(),

                TextInput::make('title')
                    ->label('Заголовок')
                    ->default('Контакты')
                    ->required(),

                Textarea::make('description')
                    ->label('Описание')
                    ->rows(2)
                    ->default('Свяжитесь с нами удобным способом. Мы работаем для вас каждый день!')
                    ->nullable(),

                TextInput::make('metro')
                    ->label('Метро / ориентир')
                    ->default('м. Юго-Западная')
                    ->nullable(),

                TextInput::make('work_week')
                    ->label('Часы работы: Пн–Пт')
                    ->default('9:00 - 21:00')
                    ->nullable(),

                TextInput::make('work_weekend')
                    ->label('Часы работы: Сб–Вс')
                    ->default('10:00 - 20:00')
                    ->nullable(),

                TextInput::make('cta_title')
                    ->label('CTA: заголовок')
                    ->default('Остались вопросы?')
                    ->nullable(),

                Textarea::make('cta_description')
                    ->label('CTA: описание')
                    ->rows(2)
                    ->default('Оставьте заявку, и наш специалист перезвонит вам в течение 5 минут для бесплатной консультации.')
                    ->nullable(),

                Repeater::make('cta_features')
                    ->label('CTA: список преимуществ')
                    ->simple(
                        TextInput::make('item')
                            ->label('Пункт')
                            ->required()
                    )
                    ->default([
                        ['item' => 'Бесплатная консультация'],
                        ['item' => 'Подбор оптимальной системы'],
                        ['item' => 'Расчет стоимости установки'],
                    ])
                    ->minItems(1)
                    ->maxItems(10),

                TextInput::make('cta_footer_note')
                    ->label('CTA: подпись под кнопкой')
                    ->default('Работаем ежедневно с 9:00 до 21:00')
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
                    ->default([
                        ['value' => '15+', 'label' => 'лет на рынке'],
                        ['value' => '5000+', 'label' => 'довольных клиентов'],
                        ['value' => '24/7', 'label' => 'поддержка клиентов'],
                    ])
                    ->minItems(1)
                    ->maxItems(6)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => ($state['value'] ?? '') . ' — ' . ($state['label'] ?? null)),

                Textarea::make('map_iframe')
                    ->label('Карта (iframe embed-код)')
                    ->rows(4)
                    ->placeholder('<iframe src="https://yandex.ru/map-widget/..." ...></iframe>')
                    ->nullable(),
            ]);
    }

    public static function getType(): string
    {
        return 'contact';
    }
}
