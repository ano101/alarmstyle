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

    protected $casts = [
        'utm' => 'array',
    ];
}
