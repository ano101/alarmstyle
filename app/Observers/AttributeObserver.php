<?php

namespace App\Observers;

use App\Models\Attribute;

class AttributeObserver
{
    /**
     * Handle the Attribute "created" event.
     */
    public function created(Attribute $attribute): void
    {
        //
    }

    /**
     * Handle the Attribute "updated" event.
     */
    public function updated(Attribute $attribute): void
    {
        //
    }

    /**
     * Handle the Attribute "deleted" event.
     */
    public function deleted(Attribute $attribute): void
    {
        //
    }

    /**
     * Handle the Attribute "restored" event.
     */
    public function restored(Attribute $attribute): void
    {
        //
    }

    /**
     * Handle the Attribute "force deleted" event.
     */
    public function forceDeleted(Attribute $attribute): void
    {
        //
    }
}
