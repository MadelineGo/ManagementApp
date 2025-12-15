<?php

namespace Tests\Unit\Domain;

use App\Domain\Clients\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function test_can_create_client()
    {
        $client = Client::create(
            'John',
            'Doe',
            'john@example.com',
            '123 Main St',
            '555-1234'
        );

        $this->assertEquals('John', $client->name());
        $this->assertEquals('Doe', $client->lastName());
        $this->assertEquals('john@example.com', $client->email());
        $this->assertEquals('123 Main St', $client->address());
        $this->assertEquals('555-1234', $client->phone());
        $this->assertTrue($client->isActive());
    }

    public function test_cannot_create_client_with_invalid_email()
    {
        $this->expectException(\InvalidArgumentException::class);

        Client::create(
            'John',
            'Doe',
            'not-an-email'
        );
    }
}
