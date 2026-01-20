<?php

namespace App\Support\Blocks\Definitions;

use App\Support\Blocks\Concerns\HasWidth;
use App\Support\Blocks\Contracts\BlockDefinition;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;

class TextBlock implements BlockDefinition
{
    use HasWidth;

    public static function make(): Block
    {
        return Block::make(self::getType())
            ->label('Текстовый блок')
            ->schema([
                self::widthField(),

                Textarea::make('content')
                    ->label('Текст')
                    ->rows(4)
                    ->required(),
            ]);
    }

    public static function getType(): string
    {
        return 'text';
    }
}
