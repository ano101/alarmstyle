<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallbackRequest extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'comment',
        'page_url',
        'utm',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'utm' => 'array',
        ];
    }

    /**
     * Scope для новых заявок
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope для обработанных заявок
     */
    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    /**
     * Отметить как обработанную
     */
    public function markAsProcessed(): bool
    {
        return $this->update(['status' => 'processed']);
    }

    /**
     * Отметить как спам
     */
    public function markAsSpam(): bool
    {
        return $this->update(['status' => 'spam']);
    }
}
