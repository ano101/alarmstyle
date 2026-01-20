<?php

namespace App\Support\Blocks\Contracts;

use Filament\Forms\Components\Builder\Block;

interface BlockDefinition
{
    public static function make(): Block;

    public static function getType(): string;
}
