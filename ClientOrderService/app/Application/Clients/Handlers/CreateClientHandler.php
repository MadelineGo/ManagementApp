<?php

namespace App\Application\Clients\Handlers;

use App\Application\Clients\Commands\CreateClientCommand;
use App\Domain\Clients\Client;
use App\Domain\Clients\ClientRepositoryInterface;
use App\Domain\Shared\UnitOfWorkInterface;
use Exception;

class CreateClientHandler
{
    private ClientRepositoryInterface $clientRepository;
    private UnitOfWorkInterface $unitOfWork;

    // Dependencias inyectadas (Controlador de Inversión)
    public function __construct(
        ClientRepositoryInterface $clientRepository,
        UnitOfWorkInterface $unitOfWork
    ) {
        $this->clientRepository = $clientRepository;
        $this->unitOfWork = $unitOfWork;
    }

    public function handle(CreateClientCommand $command): Client
    {
        // 1. Regla de Negocio: Verificar unicidad antes de crear
        if ($this->clientRepository->findByEmail($command->email)) {
            throw new Exception("El cliente con este correo electrónico ya existe.");
        }

            $client = Client::create(
            $command->name,
            $command->lastName,
            $command->email,
            $command->address,
            $command->phoneNumber
        );

        // 3. Persistencia y Transacción (Unit of Work)
        $this->unitOfWork->beginTransaction();
        try {
            // La entidad se registra para ser guardada
            $this->clientRepository->add($client);

            // Si hubiera otra operación atómica, iría aquí (ej. registrar actividad)

            $this->unitOfWork->commit(); // Ejecuta todas las operaciones pendientes

            return $client;
        } catch (Exception $e) {
            $this->unitOfWork->rollback();
            throw $e; // Re-lanzar el error para que el Controller lo maneje
        }
    }
}
