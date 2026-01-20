<?php

namespace App\Support\Blocks;

use App\Support\Blocks\Contracts\BlockHandler;
use App\Support\Blocks\Handlers\ProductsSliderHandler;

class BlocksResolver
{
    /** @var array<string, BlockHandler> */
    protected array $handlers;

    public function __construct()
    {
        $this->handlers = [
            'products_slider' => new ProductsSliderHandler,
            // тут же зарегаем другие "умные" блоки
        ];
    }

    public function resolve(array $blocks): array
    {
        return array_map(function ($block) {
            $type = $block['type'] ?? null;

            if ($type && isset($this->handlers[$type])) {
                return $this->handlers[$type]->handle($block);
            }

            // простые блоки возвращаем как есть
            return $block;
        }, $blocks);
    }
}
