<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Side Rendering
    |--------------------------------------------------------------------------
    |
    | These options configures if and how Inertia uses Server Side Rendering
    | to pre-render the initial visits made to your application's pages.
    |
    | You can specify the URL to use, and the automatically detect
    | changes to your Inertia page components and restart the SSR server.
    |
    | Do note that enabling these options will NOT automatically make SSR work,
    | as a separate process needs to be running. To learn more, please visit:
    | https://inertiajs.com/server-side-rendering
    |
    */

    'ssr' => [

        'enabled' => env('INERTIA_SSR_ENABLED', true),

        'url' => env('INERTIA_SSR_URL', env('APP_ENV') === 'production' ? 'http://ssr:13714' : 'http://127.0.0.1:13714'),

        'ensure_bundle_exists' => env('INERTIA_SSR_ENSURE_BUNDLE_EXISTS', env('APP_ENV') !== 'production'),

    ],

    /*
    |--------------------------------------------------------------------------
    | Inertia Testing
    |--------------------------------------------------------------------------
    |
    | These options allow you to configure how Inertia behaves during testing.
    |
    | You can set a page that should be returned when using the "asDump"
    | testing macro and the 'fake' option is set to true.
    |
    */

    'testing' => [

        'ensure_pages_exist' => true,

        'page_paths' => [

            resource_path('js/Pages'),

        ],

        'page_extensions' => [

            'js',
            'jsx',
            'svelte',
            'ts',
            'tsx',
            'vue',

        ],

    ],

];
