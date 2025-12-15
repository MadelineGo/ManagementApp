<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a client first
        $this->withHeaders($this->authenticate())
             ->postJson('/api/clients', [
                'name' => 'Client',
                'last_name' => 'One',
                'email' => 'client1@example.com'
            ]);
    }

    public function test_can_create_order()
    {
        $response = $this->withHeaders($this->authenticate())
            ->postJson('/api/orders', [
                'client_id' => 1,
                'amount' => 150.00,
                'description' => 'Test Order'
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Order created successfully']);
    }

    public function test_can_mark_order_completed()
    {
        // Create order
        $this->withHeaders($this->authenticate())
            ->postJson('/api/orders', [
                'client_id' => 1,
                'amount' => 100.00,
                'description' => 'To Complete'
            ]);

        // Update status
        $response = $this->withHeaders($this->authenticate())
            ->putJson('/api/orders/1', [
                'status' => 'completed'
            ]);

        $response->assertStatus(200);
    }
}
