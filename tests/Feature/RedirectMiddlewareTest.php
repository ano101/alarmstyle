<?php

namespace Tests\Feature;

use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_redirect_is_performed(): void
    {
        Redirect::create([
            'from_url' => '/old-page',
            'to_url' => '/new-page',
            'status_code' => 301,
            'is_active' => true,
        ]);

        $response = $this->get('/old-page');

        $response->assertRedirect('/new-page');
        $response->assertStatus(301);
    }

    public function test_inactive_redirect_is_not_performed(): void
    {
        Redirect::create([
            'from_url' => '/old-page',
            'to_url' => '/new-page',
            'status_code' => 301,
            'is_active' => false,
        ]);

        $response = $this->get('/old-page');

        $response->assertStatus(404);
    }

    public function test_redirect_with_query_string(): void
    {
        Redirect::create([
            'from_url' => '/old-page',
            'to_url' => '/new-page',
            'status_code' => 302,
            'is_active' => true,
        ]);

        $response = $this->get('/old-page?param=value');

        $response->assertRedirect('/new-page?param=value');
        $response->assertStatus(302);
    }

    public function test_redirect_respects_status_code(): void
    {
        Redirect::create([
            'from_url' => '/temp-page',
            'to_url' => '/destination',
            'status_code' => 302,
            'is_active' => true,
        ]);

        $response = $this->get('/temp-page');

        $response->assertRedirect('/destination');
        $response->assertStatus(302);
    }

    public function test_no_redirect_when_path_does_not_match(): void
    {
        Redirect::create([
            'from_url' => '/old-page',
            'to_url' => '/new-page',
            'status_code' => 301,
            'is_active' => true,
        ]);

        $response = $this->get('/other-page');

        $response->assertStatus(404);
    }
}
