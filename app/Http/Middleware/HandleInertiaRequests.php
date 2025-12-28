<?php

namespace App\Http\Middleware;

use App\Facades\Seo;
use App\Facades\JsonLd;
use App\Models\Menu;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'pageTitle'   => null,
            'breadcrumbs' => [],
            'jsonLd'     => fn () => JsonLd::get(),
            'seo' => fn () => [
                'title'       => Seo::getTitle() ?? config('app.name'),
                'description' => Seo::getDescription(),
                'keywords'    => Seo::getKeywords(),
                'canonical'   => Seo::getCanonical(),
                'noindex'     => Seo::isNoIndex(),

                'og_title'       => Seo::getOgTitle(),
                'og_description' => Seo::getOgDescription(),
                'og_image'       => Seo::getOgImage(),
                'og_type'        => Seo::getOgType(),

                'twitter_card'        => Seo::getTwitterCard(),
                'twitter_title'       => Seo::getTwitterTitle(),
                'twitter_description' => Seo::getTwitterDescription(),
                'twitter_image'       => Seo::getTwitterImage(),

                'url' => url()->current(),
            ],
            'settings' => fn () => [
                'company' => [
                    'name'    => setting('company.name', 'Alarmstyle'),
                    'tagline' => setting('company.tagline', 'Противоугонные системы и автоэлектроника'),
                ],
                'contacts' => [
                    'phone'    => setting('contacts.phone', '+7 (999) 123-45-67'),
                    'whatsapp' => setting('contacts.whatsapp'),
                    'telegram' => setting('contacts.telegram'),
                    'address'  => setting('contacts.address'),
                    'email'    => setting('contacts.email'),
                ],
            ],
            'menus' => fn () => [
                'header' => optional(
                        Menu::query()
                            ->where('key', 'header') // ключ меню в БД
                            ->where('is_active', true)
                            ->first()
                    )?->tree() ?? [],

            ],
        ]);
    }
}
