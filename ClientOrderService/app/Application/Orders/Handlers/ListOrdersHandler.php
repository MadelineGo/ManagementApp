<?php

namespace App\Application\Orders\Handlers;

use App\Application\Orders\Queries\ListOrdersQuery;
use App\Domain\Orders\OrderRepositoryInterface;

class ListOrdersHandler
{
    public function __construct(
        private OrderRepositoryInterface $repository
    ) {}

    public function handle(ListOrdersQuery $query): array
    {
        $filters = array_filter([
            'status' => $query->status,
            'client_id' => $query->clientId,
        ], fn($value) => !is_null($value));

        return $this->repository->search($filters);
    }
}
