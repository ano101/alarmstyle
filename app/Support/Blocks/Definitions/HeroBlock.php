<?php

namespace App\Support\Blocks\Definitions;

use App\Support\Blocks\Concerns\HasWidth;
use App\Support\Blocks\Contracts\BlockDefinition;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class HeroBlock implements BlockDefinition
{
    use HasWidth;

    public static function make(): Block
    {
        return Block::make(self::getType())
            ->label('Hero секция')
            ->schema([
                self::widthField(),

                TextInput::make('title')
                    ->label('Заголовок')
                    ->required(),

                Textarea::make('description')
                    ->label('Описание')
                    ->rows(3)
                    ->nullable(),

                FileUpload::make('background')
                    ->label('Фоновое изображение')
                    ->image()
                    ->nullable(),

                TextInput::make('button_text')
                    ->label('Текст кнопки')
                    ->nullable(),

                TextInput::make('button_url')
                    ->label('URL кнопки')
                    ->url()
                    ->nullable(),
            ]);
    }

    public static function getType(): string
    {
        return 'hero';
    }

    protected static function getDefaultWidth(): string
    {
        return 'full';
    }
}
