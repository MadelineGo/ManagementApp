<?php

namespace App\Application\Orders\Commands;

class CreateOrderCommand
{
    public function __construct(
        public readonly int $clientId,
        public readonly float $amount,
        public readonly string $description
    ) {}
}
