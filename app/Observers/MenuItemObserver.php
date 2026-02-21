<?php

namespace App\Observers;

use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuItemObserver
{
    /**
     * Handle the MenuItem "created" event.
     */
    public function created(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    /**
     * Handle the MenuItem "updated" event.
     */
    public function updated(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    /**
     * Handle the MenuItem "deleted" event.
     */
    public function deleted(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    /**
     * Handle the MenuItem "restored" event.
     */
    public function restored(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    /**
     * Handle the MenuItem "force deleted" event.
     */
    public function forceDeleted(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    /**
     * Очистка кэша меню, к которому принадлежит пункт.
     */
    protected function clearMenuCache(MenuItem $menuItem): void
    {
        $menuKey = $menuItem->menu?->key ?? $menuItem->menu()->value('key');

        if ($menuKey) {
            Cache::forget("menu:{$menuKey}");
        }
    }
}
