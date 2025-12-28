<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    protected $table = 'seo_metas';

    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'noindex',
        'extra',
    ];

    protected $casts = [
        'noindex' => 'boolean',
        'extra'   => 'array',
    ];

    public function metaable(): MorphTo
    {
        return $this->morphTo();
    }
}
