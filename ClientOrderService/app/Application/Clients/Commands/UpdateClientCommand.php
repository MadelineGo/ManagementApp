<?php

namespace App\Application\Clients\Commands;

class UpdateClientCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $address
    ) {}
}
