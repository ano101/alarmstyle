<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Seo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'seo'; // ключ биндинга в контейнере
    }
}
