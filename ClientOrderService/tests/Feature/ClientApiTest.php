<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_client()
    {
        $response = $this->withHeaders($this->authenticate())
            ->postJson('/api/clients', [
                'name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane@example.com',
                'address' => '456 Test Ave',
                'phone_number' => '123-456-7890'
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane@example.com'
            ]);

        $this->assertDatabaseHas('clients', ['email' => 'jane@example.com']);
    }

    public function test_can_list_clients()
    {
        $this->withHeaders($this->authenticate())
            ->postJson('/api/clients', [
                'name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane@example.com'
            ]);

        $response = $this->withHeaders($this->authenticate())
            ->getJson('/api/clients');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Jane']);
    }

    public function test_validates_required_fields()
    {
        $response = $this->withHeaders($this->authenticate())
            ->postJson('/api/clients', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'last_name', 'email']);
    }
}
