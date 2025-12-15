<?php

namespace App\Application\Clients\Queries;

class ListClientsQuery
{
    // Potentially add filters here (e.g. name, email)
    public function __construct(
        public ?string $name = null,
        public ?string $email = null
    ) {}
}
