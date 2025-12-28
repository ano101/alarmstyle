<?php

namespace App\Providers;

use App\Services\JsonLdService;
use App\Services\SeoService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Один инстанс на запрос
        $this->app->singleton('seo', function ($app) {
            return new SeoService();
        });

        $this->app->singleton('JsonLd', function ($app) {
            return new JsonLdService();
        });
    }

    public function boot(): void
    {
        // Шарим seo сервис во все вьюхи как переменную $seo
        View::composer('*', function ($view) {
            $view->with('seo', app('seo'));
            $view->with('JsonLd', app('JsonLd'));
        });
    }
}
