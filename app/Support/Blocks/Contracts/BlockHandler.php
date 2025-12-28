<?php

namespace App\Support\Blocks\Contracts;

interface BlockHandler
{
    public function handle(array $block): array;
}
