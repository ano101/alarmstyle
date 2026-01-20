<?php

namespace App\Support\Blocks\Concerns;

use Filament\Forms\Components\Select;

trait HasWidth
{
    protected static function widthField(): Select
    {
        return Select::make('width')
            ->label('Ширина блока')
            ->options([
                'full' => 'Во всю ширину',
                'container' => 'По ширине контейнера',
            ])
            ->default(static::getDefaultWidth())
            ->required();
    }

    protected static function getDefaultWidth(): string
    {
        return 'container';
    }
}
