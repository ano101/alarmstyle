<?php

namespace Tests\Feature;

use App\Jobs\GenerateSitemap;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_controller_returns_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    public function test_sitemap_contains_homepage(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertSee('<loc>'.url('/').'</loc>', false);
    }

    public function test_sitemap_job_generates_file(): void
    {
        Storage::fake('public');

        GenerateSitemap::dispatchSync();

        Storage::disk('public')->assertExists('sitemap.xml');

        $content = Storage::disk('public')->get('sitemap.xml');
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $content);
    }

    public function test_sitemap_includes_active_categories(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'name' => 'Test Category',
            'is_active' => true,
        ]);
        $category->setSlug('test-category');

        GenerateSitemap::dispatchSync();

        $content = Storage::disk('public')->get('sitemap.xml');
        $this->assertStringContainsString('/category/test-category', $content);
    }

    public function test_sitemap_includes_products(): void
    {
        Storage::fake('public');

        $product = Product::create([
            'name' => 'Test Product',
        ]);
        $product->setSlug('test-product');

        GenerateSitemap::dispatchSync();

        $content = Storage::disk('public')->get('sitemap.xml');
        $this->assertStringContainsString('/product/test-product', $content);
    }

    public function test_sitemap_includes_published_pages(): void
    {
        Storage::fake('public');

        $page = Page::create([
            'title' => 'Test Page',
            'is_published' => true,
            'is_homepage' => false,
        ]);
        $page->setSlug('test-page');

        GenerateSitemap::dispatchSync();

        $content = Storage::disk('public')->get('sitemap.xml');
        $this->assertStringContainsString('/test-page', $content);
    }

    public function test_sitemap_excludes_homepage_page_slug(): void
    {
        Storage::fake('public');

        $page = Page::create([
            'title' => 'Homepage',
            'is_published' => true,
            'is_homepage' => true,
        ]);

        GenerateSitemap::dispatchSync();

        $content = Storage::disk('public')->get('sitemap.xml');

        // Должна быть только одна главная страница с url('/')
        $this->assertEquals(1, substr_count($content, '<loc>'.url('/').'</loc>'));
    }

    public function test_sitemap_command_generates_successfully(): void
    {
        Storage::fake('public');

        $this->artisan('sitemap:generate')
            ->expectsOutput('Generating sitemap...')
            ->expectsOutput('Sitemap generated successfully at storage/app/public/sitemap.xml')
            ->assertExitCode(0);

        Storage::disk('public')->assertExists('sitemap.xml');
    }
}
