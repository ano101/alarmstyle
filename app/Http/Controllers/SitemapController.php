<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateSitemap;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $disk = Storage::disk('public');

        if (! $disk->exists('sitemap.xml')) {
            // Генерируем синхронно, если файл не существует
            GenerateSitemap::dispatchSync();
        }

        $content = $disk->get('sitemap.xml');

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
