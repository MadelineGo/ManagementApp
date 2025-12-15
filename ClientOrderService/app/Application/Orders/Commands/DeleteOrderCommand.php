<?php

namespace App\Application\Orders\Commands;

class DeleteOrderCommand
{
    public function __construct(
        public int $id
    ) {}
}
