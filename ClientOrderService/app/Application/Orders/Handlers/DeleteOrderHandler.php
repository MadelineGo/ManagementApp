<?php

namespace App\Application\Orders\Handlers;

use App\Application\Orders\Commands\DeleteOrderCommand;
use App\Domain\Orders\OrderRepositoryInterface;

class DeleteOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $repository
    ) {}

    public function handle(DeleteOrderCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
