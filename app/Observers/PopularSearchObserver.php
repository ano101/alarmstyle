<?php

namespace App\Observers;

use App\Models\PopularSearch;
use Illuminate\Support\Facades\Cache;

class PopularSearchObserver
{
    /**
     * Handle the PopularSearch "created" event.
     */
    public function created(PopularSearch $popularSearch): void
    {
        $this->clearCache();
    }

    /**
     * Handle the PopularSearch "updated" event.
     */
    public function updated(PopularSearch $popularSearch): void
    {
        $this->clearCache();
    }

    /**
     * Handle the PopularSearch "deleted" event.
     */
    public function deleted(PopularSearch $popularSearch): void
    {
        $this->clearCache();
    }

    /**
     * Handle the PopularSearch "restored" event.
     */
    public function restored(PopularSearch $popularSearch): void
    {
        $this->clearCache();
    }

    /**
     * Handle the PopularSearch "force deleted" event.
     */
    public function forceDeleted(PopularSearch $popularSearch): void
    {
        $this->clearCache();
    }

    /**
     * Очистка кэша популярных поисковых запросов
     */
    protected function clearCache(): void
    {
        Cache::forget('popular_searches');
    }
}
