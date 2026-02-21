<?php

namespace Tests\Feature\Services;

use App\Models\CallbackRequest;
use App\Services\CallbackRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CallbackRequestServiceTest extends TestCase
{
    use RefreshDatabase;

    private CallbackRequestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CallbackRequestService::class);
    }

    public function test_creates_callback_request_with_all_fields(): void
    {
        $request = Request::create('/callback', 'POST', [
            'name' => 'Иван Петров',
            'phone' => '+79991234567',
            'comment' => 'Хочу установить сигнализацию',
            'page_url' => 'https://example.com/catalog',
        ]);

        $callback = $this->service->createFromRequest($request);

        $this->assertInstanceOf(CallbackRequest::class, $callback);
        $this->assertDatabaseHas(CallbackRequest::class, [
            'name' => 'Иван Петров',
            'phone' => '+79991234567',
            'comment' => 'Хочу установить сигнализацию',
            'page_url' => 'https://example.com/catalog',
            'status' => 'new',
        ]);
    }

    public function test_uses_referer_when_page_url_is_empty(): void
    {
        $request = Request::create('/callback', 'POST', [
            'name' => 'Тест',
            'phone' => '+79990000000',
        ]);
        $request->headers->set('referer', 'https://example.com/product/pandora');

        $this->service->createFromRequest($request);

        $this->assertDatabaseHas(CallbackRequest::class, [
            'page_url' => 'https://example.com/product/pandora',
        ]);
    }

    public function test_utm_is_null_when_no_utm_params_provided(): void
    {
        $request = Request::create('/callback', 'POST', [
            'name' => 'Тест',
            'phone' => '+79990000000',
        ]);

        $callback = $this->service->createFromRequest($request);

        $this->assertNull($callback->utm);
    }

    public function test_saves_utm_from_nested_utm_array(): void
    {
        $request = Request::create('/callback', 'POST', [
            'name' => 'Тест',
            'phone' => '+79990000000',
            'utm' => [
                'utm_source' => 'google',
                'utm_medium' => 'cpc',
                'utm_campaign' => 'summer_sale',
            ],
        ]);

        $callback = $this->service->createFromRequest($request);

        $this->assertEquals([
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'summer_sale',
        ], $callback->utm);
    }

    public function test_collect_utm_params_from_query_string(): void
    {
        $request = Request::create('/callback?utm_source=yandex&utm_medium=organic', 'POST', [
            'name' => 'Тест',
            'phone' => '+79990000000',
        ]);

        $utm = $this->service->collectUtmParams($request);

        $this->assertEquals([
            'utm_source' => 'yandex',
            'utm_medium' => 'organic',
        ], $utm);
    }

    public function test_collect_utm_params_merges_array_and_query(): void
    {
        $request = Request::create('/callback?utm_term=pandora', 'POST', [
            'name' => 'Тест',
            'phone' => '+79990000000',
            'utm' => [
                'utm_source' => 'google',
            ],
        ]);

        $utm = $this->service->collectUtmParams($request);

        $this->assertArrayHasKey('utm_source', $utm);
        $this->assertArrayHasKey('utm_term', $utm);
        $this->assertEquals('google', $utm['utm_source']);
        $this->assertEquals('pandora', $utm['utm_term']);
    }

    public function test_collect_utm_params_filters_empty_values(): void
    {
        $request = Request::create('/callback', 'POST', [
            'name' => 'Тест',
            'phone' => '+79990000000',
            'utm' => [
                'utm_source' => 'google',
                'utm_medium' => '',
                'utm_campaign' => null,
            ],
        ]);

        $utm = $this->service->collectUtmParams($request);

        $this->assertArrayHasKey('utm_source', $utm);
        $this->assertArrayNotHasKey('utm_medium', $utm);
        $this->assertArrayNotHasKey('utm_campaign', $utm);
    }

    public function test_collect_utm_params_ignores_unknown_query_keys(): void
    {
        $request = Request::create('/callback?utm_source=google&random_param=value&utm_content=ad1', 'POST', [
            'name' => 'Тест',
            'phone' => '+79990000000',
        ]);

        $utm = $this->service->collectUtmParams($request);

        $this->assertArrayHasKey('utm_source', $utm);
        $this->assertArrayHasKey('utm_content', $utm);
        $this->assertArrayNotHasKey('random_param', $utm);
    }

    public function test_collect_utm_params_returns_empty_array_when_no_utm(): void
    {
        $request = Request::create('/callback', 'POST', [
            'name' => 'Тест',
            'phone' => '+79990000000',
        ]);

        $utm = $this->service->collectUtmParams($request);

        $this->assertEmpty($utm);
    }
}
