<?php

namespace App\Application\Clients\Handlers;

use App\Application\Clients\Queries\ListClientsQuery;
use App\Domain\Clients\ClientRepositoryInterface;

class ListClientsHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository
    ) {}

    public function handle(ListClientsQuery $query): array
    {
        return $this->clientRepository->findAll();
    }
}
