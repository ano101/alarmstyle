<?php

namespace Tests\Feature\Services;

use App\Facades\JsonLd;
use App\Facades\Seo;
use App\Models\Page;
use App\Services\JsonLdService;
use App\Services\PageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PageServiceTest extends TestCase
{
    use RefreshDatabase;

    private PageService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PageService::class);

        // Сбрасываем синглтоны перед каждым тестом
        app()->forgetInstance('seo');
        app()->forgetInstance(JsonLdService::class);
    }

    // ─── getBreadcrumbs ─────────────────────────────────────────────────────

    public function test_get_breadcrumbs_returns_null_for_homepage(): void
    {
        $page = Page::create([
            'title' => 'Главная',
            'is_homepage' => true,
            'is_published' => true,
        ]);

        $breadcrumbs = $this->service->getBreadcrumbs($page);

        $this->assertNull($breadcrumbs);
    }

    public function test_get_breadcrumbs_returns_array_for_regular_page(): void
    {
        $page = Page::create([
            'title' => 'О нас',
            'is_homepage' => false,
            'is_published' => true,
        ]);
        $page->setSlug('about');

        $breadcrumbs = $this->service->getBreadcrumbs($page);

        $this->assertIsArray($breadcrumbs);
        $this->assertNotEmpty($breadcrumbs);
    }

    public function test_get_breadcrumbs_first_item_is_home(): void
    {
        $page = Page::create([
            'title' => 'Контакты',
            'is_homepage' => false,
            'is_published' => true,
        ]);
        $page->setSlug('contacts');

        $breadcrumbs = $this->service->getBreadcrumbs($page);

        $this->assertEquals('Главная', $breadcrumbs[0]['label']);
    }

    public function test_get_breadcrumbs_last_item_has_page_title(): void
    {
        $page = Page::create([
            'title' => 'Доставка',
            'is_homepage' => false,
            'is_published' => true,
        ]);
        $page->setSlug('delivery');

        $breadcrumbs = $this->service->getBreadcrumbs($page);

        $lastItem = end($breadcrumbs);
        $this->assertEquals('Доставка', $lastItem['label']);
    }

    // ─── applySeo ───────────────────────────────────────────────────────────

    public function test_apply_seo_sets_og_title_fallback_to_page_title(): void
    {
        $page = Page::create([
            'title' => 'О компании',
            'is_homepage' => false,
            'is_published' => true,
        ]);
        $page->load('seoMeta');

        $this->service->applySeo($page, Request::create('/about'));

        $this->assertEquals('О компании', Seo::getOgTitle());
    }

    public function test_apply_seo_sets_og_type_fallback_to_website(): void
    {
        $page = Page::create([
            'title' => 'Страница',
            'is_homepage' => false,
            'is_published' => true,
        ]);
        $page->load('seoMeta');

        $this->service->applySeo($page, Request::create('/page'));

        $this->assertEquals('website', Seo::getOgType());
    }

    public function test_apply_seo_sets_canonical_to_full_request_url(): void
    {
        $page = Page::create([
            'title' => 'Страница',
            'is_homepage' => false,
            'is_published' => true,
        ]);
        $page->load('seoMeta');

        $this->service->applySeo($page, Request::create('https://example.com/about'));

        $this->assertEquals('https://example.com/about', Seo::getCanonical());
    }

    // ─── applyJsonLd ────────────────────────────────────────────────────────

    public function test_apply_json_ld_adds_website_schema_for_homepage(): void
    {
        $page = Page::create([
            'title' => 'Главная',
            'is_homepage' => true,
            'is_published' => true,
        ]);
        $page->load('seoMeta');

        $request = Request::create('https://example.com/');
        $this->service->applySeo($page, $request);
        $this->service->applyJsonLd($page, $request, null);

        $types = array_column(JsonLd::getArray(), '@type');
        $this->assertContains('WebSite', $types);
    }

    public function test_apply_json_ld_website_has_search_action(): void
    {
        $page = Page::create([
            'title' => 'Главная',
            'is_homepage' => true,
            'is_published' => true,
        ]);
        $page->load('seoMeta');

        $request = Request::create('https://example.com/');
        $this->service->applySeo($page, $request);
        $this->service->applyJsonLd($page, $request, null);

        $websiteSchema = collect(JsonLd::getArray())->firstWhere('@type', 'WebSite');

        $this->assertNotNull($websiteSchema);
        $this->assertArrayHasKey('potentialAction', $websiteSchema);
        $this->assertEquals('SearchAction', $websiteSchema['potentialAction']['@type']);
    }

    public function test_apply_json_ld_adds_webpage_schema_for_regular_page(): void
    {
        $page = Page::create([
            'title' => 'Контакты',
            'is_homepage' => false,
            'is_published' => true,
        ]);
        $page->setSlug('contacts');
        $page->load('seoMeta');

        $request = Request::create('https://example.com/contacts');
        $breadcrumbs = $this->service->getBreadcrumbs($page);
        $this->service->applySeo($page, $request);
        $this->service->applyJsonLd($page, $request, $breadcrumbs);

        $types = array_column(JsonLd::getArray(), '@type');
        $this->assertContains('WebPage', $types);
    }

    public function test_apply_json_ld_adds_breadcrumb_list_for_regular_page(): void
    {
        $page = Page::create([
            'title' => 'Контакты',
            'is_homepage' => false,
            'is_published' => true,
        ]);
        $page->setSlug('contacts');
        $page->load('seoMeta');

        $request = Request::create('https://example.com/contacts');
        $breadcrumbs = $this->service->getBreadcrumbs($page);
        $this->service->applySeo($page, $request);
        $this->service->applyJsonLd($page, $request, $breadcrumbs);

        $types = array_column(JsonLd::getArray(), '@type');
        $this->assertContains('BreadcrumbList', $types);
    }

    public function test_apply_json_ld_does_not_add_breadcrumb_list_for_homepage(): void
    {
        $page = Page::create([
            'title' => 'Главная',
            'is_homepage' => true,
            'is_published' => true,
        ]);
        $page->load('seoMeta');

        $request = Request::create('https://example.com/');
        $this->service->applySeo($page, $request);
        $this->service->applyJsonLd($page, $request, null);

        $types = array_column(JsonLd::getArray(), '@type');
        $this->assertNotContains('BreadcrumbList', $types);
    }

    public function test_breadcrumb_list_items_have_correct_position(): void
    {
        $page = Page::create([
            'title' => 'О нас',
            'is_homepage' => false,
            'is_published' => true,
        ]);
        $page->setSlug('about');
        $page->load('seoMeta');

        $request = Request::create('https://example.com/about');
        $breadcrumbs = $this->service->getBreadcrumbs($page);
        $this->service->applySeo($page, $request);
        $this->service->applyJsonLd($page, $request, $breadcrumbs);

        $breadcrumbSchema = collect(JsonLd::getArray())->firstWhere('@type', 'BreadcrumbList');
        $positions = array_column($breadcrumbSchema['itemListElement'], 'position');

        $this->assertEquals([1, 2], $positions);
    }
}
