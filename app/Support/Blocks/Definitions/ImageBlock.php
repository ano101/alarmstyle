<?php

namespace App\Support\Blocks\Definitions;

use App\Support\Blocks\Concerns\HasWidth;
use App\Support\Blocks\Contracts\BlockDefinition;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class ImageBlock implements BlockDefinition
{
    use HasWidth;

    public static function make(): Block
    {
        return Block::make(self::getType())
            ->label('Картинка')
            ->schema([
                self::widthField(),

                FileUpload::make('url')
                    ->label('Картинка')
                    ->image()
                    ->required(),
                TextInput::make('alt')
                    ->label('Alt')
                    ->nullable(),
            ]);
    }

    public static function getType(): string
    {
        return 'image';
    }
}
