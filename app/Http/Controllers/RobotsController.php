<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = [];

        $lines[] = 'User-agent: *';

        // Можно хранить массив/строку в настройках админки
        $disallow = setting('seo.robots_disallow', ['/admin', '/login']);

        foreach ((array) $disallow as $path) {
            $lines[] = 'Disallow: ' . $path;
        }

        if ($sitemap = setting('seo.sitemap_url', url('sitemap.xml'))) {
            $lines[] = 'Sitemap: ' . $sitemap;
        }

        $content = implode(PHP_EOL, $lines) . PHP_EOL;

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
