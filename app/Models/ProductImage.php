<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = ['url', 'product_id', 'sort_order'];

    protected static function booted(): void
    {
        static::creating(function (ProductImage $image) {
            if (is_null($image->sort_order)) {
                $image->sort_order = static::where('product_id', $image->product_id)->max('sort_order') + 1;
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
