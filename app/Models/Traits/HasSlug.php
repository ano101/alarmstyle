<?php

namespace App\Models\Traits;

use App\Models\Slug;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSlug
{
    /**
     * Полиморфная связь slug → модель
     */
    public function slug(): MorphOne
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    /**
     * Установить slug (создать или обновить)
     */
    public function setSlug(string $slug): void
    {
        $this->slug()->updateOrCreate([], [
            'slug' => $slug,
        ]);
    }

    /**
     * Получить slug строки
     */
    public function getSlug(): ?string
    {
        return $this->slug?->slug;
    }

    /**
     * Удобный аксессор $model->url
     * (кастомизируй под свой роутинг)
     */
    public function getUrlAttribute(): ?string
    {
        return $this->slug ? '/'.$this->slug->slug : null;
    }

    /**
     * Автоматическое удаление slug при delete()
     */
    public static function bootHasSlug(): void
    {
        static::deleting(function ($model) {
            $model->slug()->delete();
        });
    }
}
