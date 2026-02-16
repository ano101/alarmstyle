<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\PopularSearch;
use App\Observers\MenuObserver;
use App\Observers\PopularSearchObserver;
use App\Services\BreadcrumbService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BreadcrumbService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Регистрируем observers для автоматической очистки кэша
        Menu::observe(MenuObserver::class);
        PopularSearch::observe(PopularSearchObserver::class);
    }
}


