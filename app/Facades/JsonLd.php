<?php

namespace App\Facades;

use App\Services\JsonLdService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Services\JsonLdService add(array $schema)
 * @method static \App\Services\JsonLdService set(array $schemas)
 * @method static \App\Services\JsonLdService clear()
 * @method static array getArray()
 * @method static ?string get()
 */
class JsonLd extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return JsonLdService::class;
    }
}
