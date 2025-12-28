<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'key',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort');
    }

    public function rootItems(): HasMany
    {
        return $this->items()->whereNull('parent_id');
    }

    public function tree(): array
    {
        $this->loadMissing([
            'rootItems.childrenRecursive',
        ]);

        $map = function (MenuItem $item) use (&$map) {
            return [
                'label'    => $item->label,
                'href'     => $item->resolved_href,
                'icon'     => $item->icon,
                'newTab'   => (bool) $item->open_in_new_tab,
                'children' => $item->children->map(fn ($c) => $map($c))->all(),
            ];
        };

        return $this->rootItems->map(fn ($i) => $map($i))->all();
    }
}
