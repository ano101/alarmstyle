<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\CategoryLanding;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $xml = $this->generateSitemapXml();

        Storage::disk('public')->put('sitemap.xml', $xml);
    }

    protected function generateSitemapXml(): string
    {
        $urls = [];

        // Главная страница
        $urls[] = [
            'loc' => url('/'),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Страницы
        $pages = Page::where('is_published', true)->get();
        foreach ($pages as $page) {
            if ($page->is_homepage) {
                continue; // уже добавили главную
            }

            $slug = $page->getSlug();
            if ($slug) {
                $urls[] = [
                    'loc' => url('/'.$slug),
                    'lastmod' => $page->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }
        }

        // Категории
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $slug = $category->getSlug();
            if ($slug) {
                $urls[] = [
                    'loc' => url('/category/'.$slug),
                    'lastmod' => $category->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }
        }

        // Посадочные страницы категорий
        $landings = CategoryLanding::with('category')->get();
        foreach ($landings as $landing) {
            $seoMeta = $landing->seoMeta()->first();
            if ($seoMeta && $seoMeta->canonical_url) {
                $urls[] = [
                    'loc' => url($seoMeta->canonical_url),
                    'lastmod' => $landing->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }
        }

        // Товары
        $products = Product::whereNull('deleted_at')->get();
        foreach ($products as $product) {
            $slug = $product->getSlug();
            if ($slug) {
                $urls[] = [
                    'loc' => url('/product/'.$slug),
                    'lastmod' => $product->updated_at->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            }
        }

        return $this->buildXml($urls);
    }

    protected function buildXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>'.PHP_EOL;
            $xml .= '    <loc>'.htmlspecialchars($url['loc']).'</loc>'.PHP_EOL;

            if (isset($url['lastmod'])) {
                $xml .= '    <lastmod>'.$url['lastmod'].'</lastmod>'.PHP_EOL;
            }

            if (isset($url['changefreq'])) {
                $xml .= '    <changefreq>'.$url['changefreq'].'</changefreq>'.PHP_EOL;
            }

            if (isset($url['priority'])) {
                $xml .= '    <priority>'.$url['priority'].'</priority>'.PHP_EOL;
            }

            $xml .= '  </url>'.PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
