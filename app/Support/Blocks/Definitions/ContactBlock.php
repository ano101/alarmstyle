<?php

namespace App\Support\Blocks\Definitions;

use App\Support\Blocks\Concerns\HasWidth;
use App\Support\Blocks\Contracts\BlockDefinition;
use Filament\Forms\Components\Builder\Block;

class ContactBlock implements BlockDefinition
{
    use HasWidth;

    public static function make(): Block
    {
        return Block::make(self::getType())
            ->label('Страница контакты')
            ->schema([
                self::widthField(),
            ]);
    }

    public static function getType(): string
    {
        return 'contact';
    }
}
