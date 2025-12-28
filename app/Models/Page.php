<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasSeo;
use App\Models\Traits\HasSlug;
use App\Models\Traits\HasBlocks;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;
    use HasSlug;
    use HasBlocks;
    use HasSeo;

    protected $table = 'pages';

    protected $fillable = [
        'title',
        'blocks',
        'is_published',
    ];

    protected $casts = [
        'blocks'       => 'array',
        'is_published' => 'boolean',
    ];
    protected static function booted(): void
    {
        static::saved(function (Page $page) {
            // если слага ещё нет — генерим его по title
            if (! $page->getSlug() && $page->title) {
                $page->setSlug(Str::slug($page->title));
            }
        });
    }
}
