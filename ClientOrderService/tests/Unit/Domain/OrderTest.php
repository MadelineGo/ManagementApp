<?php

namespace Tests\Unit\Domain;

use App\Domain\Orders\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function test_can_create_order()
    {
        $order = new Order(
            clientId: 1,
            amount: 100.50,
            description: 'Test Order'
        );

        $this->assertEquals(1, $order->getClientId());
        $this->assertEquals(100.50, $order->getAmount());
        $this->assertEquals('Test Order', $order->getDescription());
        $this->assertEquals('pending', $order->getStatus());
    }

    public function test_can_mark_order_as_completed()
    {
        $order = new Order(1, 100.0, 'Test');
        $order->markAsCompleted();

        $this->assertEquals('completed', $order->getStatus());
    }

    public function test_can_mark_order_as_cancelled()
    {
        $order = new Order(1, 100.0, 'Test');
        $order->markAsCancelled();

        $this->assertEquals('cancelled', $order->getStatus());
    }

    public function test_can_update_details()
    {
        $order = new Order(1, 100.0, 'Test');
        
        $order->updateAmount(200.0);
        $order->updateDescription('Updated Test');

        $this->assertEquals(200.0, $order->getAmount());
        $this->assertEquals('Updated Test', $order->getDescription());
    }
}
