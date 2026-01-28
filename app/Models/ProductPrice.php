<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasFactory;

    // Типы цен
    public const TYPE_BASE = 1; // "просто" цена

    public const TYPE_WITH_INSTALL = 2; // с установкой

    public const TYPE_WITHOUT_INSTALL = 3; // без установки

    protected $fillable = [
        'product_id',
        'price',
        'type',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'type' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Человекочитаемое имя типа цены
     */
    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_BASE => 'Цена',
            self::TYPE_WITH_INSTALL => 'Цена с установкой',
            self::TYPE_WITHOUT_INSTALL => 'Цена без установки',
            default => 'Неизвестный тип',
        };
    }

    /**
     * Скоупы для удобства, если понадобится
     */
    public function scopeBase($query)
    {
        return $query->where('type', self::TYPE_BASE);
    }

    public function scopeWithInstall($query)
    {
        return $query->where('type', self::TYPE_WITH_INSTALL);
    }

    public function scopeWithoutInstall($query)
    {
        return $query->where('type', self::TYPE_WITHOUT_INSTALL);
    }
}
