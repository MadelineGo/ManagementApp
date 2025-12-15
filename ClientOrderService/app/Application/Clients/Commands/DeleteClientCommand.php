<?php

namespace App\Application\Clients\Commands;

class DeleteClientCommand
{
    public function __construct(
        public readonly int $id
    ) {}
}
