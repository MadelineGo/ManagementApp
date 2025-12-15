<?php

namespace App\Application\Clients\Handlers;

use App\Application\Clients\Commands\DeleteClientCommand;
use App\Domain\Clients\ClientRepositoryInterface;
use App\Domain\Shared\UnitOfWorkInterface;
use Exception;

class DeleteClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly UnitOfWorkInterface $unitOfWork
    ) {}

    public function handle(DeleteClientCommand $command): void
    {
        $this->unitOfWork->beginTransaction();
        try {
            $this->clientRepository->delete($command->id);
            $this->unitOfWork->commit();
        } catch (Exception $e) {
            $this->unitOfWork->rollback();
            throw $e;
        }
    }
}
