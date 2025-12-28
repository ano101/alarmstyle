<?php

return [

    'disk'        => 'public',
    'source_path' => 'images',
    'cache_path'  => 'images/cache',

    'format'          => 'webp',
    'quality'         => 80,
    'fallback_format' => 'jpeg',
    'cache_ttl_days'  => 30,

    'presets' => [

        'product' => [
            'card' => [
                'width'   => 470,
                'height'  => 352,
                'fit'     => 'crop',
                'upscale' => false, // ⬅ не увеличивать маленькие
            ],
            'card_2x' => [
                'width'   => 940,
                'height'  => 704,
                'fit'     => 'crop',
                'upscale' => false,
            ],
        ],

        'category' => [
            'card' => [
                'width'  => 600,
                'height' => 400,
                'fit'    => 'crop',
            ],
            'banner' => [
                'width'  => 1440,
                'height' => 500,
                'fit'    => 'crop',
            ],
        ],

        'banner' => [
            'slider_full' => [
                'width'  => 1920,
                'height' => 600,
                'fit'    => 'crop',
            ],
            'slider_mobile' => [
                'width'  => 800,
                'height' => 600,
                'fit'    => 'crop',
            ],
        ],

        'user' => [
            'avatar_small' => [
                'width'  => 64,
                'height' => 64,
                'fit'    => 'crop',
            ],
            'avatar_large' => [
                'width'  => 300,
                'height' => 300,
                'fit'    => 'crop',
            ],
        ],
    ],
];
