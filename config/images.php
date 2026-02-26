<?php

return [
    'disk' => 'public',
    'source_path' => 'images',
    'cache_path' => 'images/cache',

    'format' => 'webp',
    'quality' => 80,
    'fallback_format' => 'jpeg',
    'cache_ttl_days' => 30,

    'presets' => [
        'product' => [
            // Карточка каталога: ~300-400px CSS × 2x ретина = 600-800px
            'card' => [
                'width' => 600,
                'height' => 450,
                'fit' => 'crop',
                'upscale' => true,
            ],
            // 2x ретина для карточки каталога
            'card_2x' => [
                'width' => 1200,
                'height' => 900,
                'fit' => 'crop',
                'upscale' => true,
            ],
            // Главная галерея продукта: aspect-square
            'gallery' => [
                'width' => 800,
                'height' => 800,
                'fit' => 'crop',
                'upscale' => true,
            ],
            // 2x ретина для главной галереи
            'gallery_2x' => [
                'width' => 1600,
                'height' => 1600,
                'fit' => 'crop',
                'upscale' => true,
            ],
            // Миниатюра галереи: ~120px CSS × 2x ретина = 240px
            'thumbnail' => [
                'width' => 160,
                'height' => 160,
                'fit' => 'contain',
                'upscale' => true,
            ],
            'thumbnail_2x' => [
                'width' => 320,
                'height' => 320,
                'fit' => 'contain',
                'upscale' => true,
            ],
            // Лайтбокс: оптимизировано для Full HD и 2K мониторов
            'lightbox' => [
                'width' => 1920,
                'height' => 1920,
                'fit' => 'contain',
                'upscale' => false,
            ],
            // 2x ретина для лайтбокса (для 4K мониторов и ретина дисплеев)
            'lightbox_2x' => [
                'width' => 2560,
                'height' => 2560,
                'fit' => 'contain',
                'upscale' => false,
            ],
            'og' => [
                'width' => 1200,
                'height' => 630,
                'fit' => 'contain',
                'upscale' => false,
            ],
        ],

        'category' => [
            'card' => [
                'width' => 600,
                'height' => 400,
                'fit' => 'crop',
            ],
            'banner' => [
                'width' => 1440,
                'height' => 500,
                'fit' => 'crop',
            ],
        ],

        'banner' => [
            'slider_full' => [
                'width' => 1920,
                'height' => 600,
                'fit' => 'crop',
            ],
            'slider_mobile' => [
                'width' => 800,
                'height' => 600,
                'fit' => 'crop',
            ],
        ],

        'user' => [
            'avatar_small' => [
                'width' => 64,
                'height' => 64,
                'fit' => 'crop',
            ],
            'avatar_large' => [
                'width' => 300,
                'height' => 300,
                'fit' => 'crop',
            ],
        ],

        'about' => [
            'company_image' => [
                'width' => 600,
                'height' => 400,
                'fit' => 'cover',
                'upscale' => false,
            ],
            'company_image_2x' => [
                'width' => 1200,
                'height' => 800,
                'fit' => 'cover',
                'upscale' => false,
            ],
            'license' => [
                'width' => 300,
                'height' => 400,
                'fit' => 'contain',
                'upscale' => false,
            ],
            'license_2x' => [
                'width' => 600,
                'height' => 800,
                'fit' => 'contain',
                'upscale' => false,
            ],
            'team_image' => [
                'width' => 600,
                'height' => 400,
                'fit' => 'cover',
                'upscale' => false,
            ],
            'team_image_2x' => [
                'width' => 1200,
                'height' => 800,
                'fit' => 'cover',
                'upscale' => false,
            ],
        ],
    ],
];
