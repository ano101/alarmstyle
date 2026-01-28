<?php

namespace App\Support\Blocks\Definitions;

use App\Support\Blocks\Concerns\HasWidth;
use App\Support\Blocks\Contracts\BlockDefinition;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;

class OurServicesBlock implements BlockDefinition
{
    use HasWidth;

    public static function make(): Block
    {
        return Block::make(self::getType())
            ->label('Наши услуги')
            ->schema([
                self::widthField(),

//                TextInput::make('text')
//                    ->label('Текст')
//                    ->required(),
//                TextInput::make('level')
//                    ->label('Уровень (h1–h4)')
//                    ->default('h2'),
            ]);
    }

    public static function getType(): string
    {
        return 'our_services';
    }
}
