<?php

namespace App\Observers;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;

class MenuObserver
{
    /**
     * Handle the Menu "created" event.
     */
    public function created(Menu $menu): void
    {
        $this->clearMenuCache($menu);
    }

    /**
     * Handle the Menu "updated" event.
     */
    public function updated(Menu $menu): void
    {
        $this->clearMenuCache($menu);
    }

    /**
     * Handle the Menu "deleted" event.
     */
    public function deleted(Menu $menu): void
    {
        $this->clearMenuCache($menu);
    }

    /**
     * Handle the Menu "restored" event.
     */
    public function restored(Menu $menu): void
    {
        $this->clearMenuCache($menu);
    }

    /**
     * Handle the Menu "force deleted" event.
     */
    public function forceDeleted(Menu $menu): void
    {
        $this->clearMenuCache($menu);
    }

    /**
     * Очистка кэша конкретного меню по ключу
     */
    protected function clearMenuCache(Menu $menu): void
    {
        if ($menu->key) {
            Cache::forget("menu:{$menu->key}");
        }
    }
}
