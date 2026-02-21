<?php

namespace Tests\Feature;

use App\Models\CallbackRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CallbackRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_callback_request(): void
    {
        $response = $this->post(route('callback.store'), [
            'name' => 'Иван',
            'phone' => '+79991234567',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas(CallbackRequest::class, [
            'name' => 'Иван',
            'phone' => '+79991234567',
            'status' => 'new',
        ]);
    }

    public function test_store_honeypot_returns_no_content(): void
    {
        $response = $this->post(route('callback.store'), [
            'name' => 'Бот',
            'phone' => '+79990000000',
            'website' => 'https://spam.com',
        ]);

        $response->assertNoContent();
        $this->assertDatabaseCount(CallbackRequest::class, 0);
    }
}
