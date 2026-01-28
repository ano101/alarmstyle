<?php

namespace App\Models;

use App\Models\Traits\HasBlocks;
use App\Models\Traits\HasSeo;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasBlocks;
    use HasFactory;
    use HasSeo;
    use HasSlug;

    protected $table = 'pages';

    protected $fillable = [
        'title',
        'blocks',
        'is_published',
        'is_homepage',
    ];

    protected $casts = [
        'blocks' => 'array',
        'is_published' => 'boolean',
        'is_homepage' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Page $page) {
            // Если страница помечена как главная
            if ($page->is_homepage) {
                // Снимаем флаг главной со всех других страниц
                static::where('id', '!=', $page->id)
                    ->where('is_homepage', true)
                    ->update(['is_homepage' => false]);

                // Для главной страницы слаг должен быть пустым
                $page->setSlug('');
            } else {
                // Для остальных страниц генерируем слаг по title, если его нет
                if (! $page->getSlug() && $page->title) {
                    $page->setSlug(Str::slug($page->title));
                }
            }
        });
    }
}
