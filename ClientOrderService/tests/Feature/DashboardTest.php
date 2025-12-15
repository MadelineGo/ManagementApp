<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_return_correct_structure()
    {
        // Seed data (requires auth)
        $auth = $this->authenticate();
        
        // Client
        $this->withHeaders($auth)->postJson('/api/clients', ['name' => 'C1', 'last_name' => 'L1', 'email' => 'c1@test.com']);
        // Orders
        $this->withHeaders($auth)->postJson('/api/orders', ['client_id' => 1, 'amount' => 10, 'description' => 'O1']);
        $this->withHeaders($auth)->postJson('/api/orders', ['client_id' => 1, 'amount' => 20, 'description' => 'O2']);
        
        // Mark one completed
        $this->withHeaders($auth)->putJson('/api/orders/1', ['status' => 'completed']);

        $response = $this->withHeaders($auth)->getJson('/api/dashboard/stats');

        $response->assertStatus(200)
             // Assert basic structure
            ->assertJsonStructure([
                'total_orders',
                'orders_by_status' => [
                    'completed',
                    'pending',
                    'cancelled'
                ],
                'active_clients',
                'monthly_activity'
            ]);
            
         // Values might depend on DB reset order so checking structure is safer, 
         // but since we refresh database, we expect 2 orders.
         $response->assertJson([
             'total_orders' => 2,
             'active_clients' => 1,
             'orders_by_status' => [
                 'completed' => 1,
                 'pending' => 1
             ]
         ]);
    }
}
