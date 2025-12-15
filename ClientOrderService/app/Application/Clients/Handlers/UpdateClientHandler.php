<?php

namespace App\Application\Clients\Handlers;

use App\Application\Clients\Commands\UpdateClientCommand;
use App\Domain\Clients\ClientRepositoryInterface;
use App\Domain\Shared\UnitOfWorkInterface;
use Exception;

class UpdateClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly UnitOfWorkInterface $unitOfWork
    ) {}

    public function handle(UpdateClientCommand $command): void
    {
        $client = $this->clientRepository->findById($command->id);

        if (!$client) {
            throw new Exception("Client not found.");
        }

        $client->update(
            $command->name,
            $command->email,
            $command->address
        );

        $this->unitOfWork->beginTransaction();
        try {
            $this->clientRepository->save($client);
            $this->unitOfWork->commit();
        } catch (Exception $e) {
            $this->unitOfWork->rollback();
            throw $e;
        }
    }
}
