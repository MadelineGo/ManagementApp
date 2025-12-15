<?php

namespace App\Application\Orders\Commands;

class UpdateOrderCommand
{
    public function __construct(
        public int $id,
        public ?int $clientId,
        public ?float $amount,
        public ?string $description,
        public ?string $status
    ) {}
}
