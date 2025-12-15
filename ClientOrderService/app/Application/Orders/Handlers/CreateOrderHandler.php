<?php

namespace App\Application\Orders\Handlers;

use App\Application\Orders\Commands\CreateOrderCommand;
use App\Domain\Orders\Order;
use App\Domain\Orders\OrderRepositoryInterface;
use App\Domain\Shared\UnitOfWorkInterface;
use Exception;

class CreateOrderHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly UnitOfWorkInterface $unitOfWork
    ) {}

    public function handle(CreateOrderCommand $command): void
    {
        $this->unitOfWork->beginTransaction();

        try {
            // 1. Crear la entidad de dominio (Lógica pura)
            $order = new Order(
                clientId: $command->clientId,
                amount: $command->amount,
                description: $command->description
            );

            // 2. Persistir usando el repositorio
            $this->orderRepository->save($order);

            // 3. Confirmar transacción
            $this->unitOfWork->commit();
        } catch (Exception $e) {
            // 4. Revertir en caso de error
            $this->unitOfWork->rollback();
            throw $e;
        }
    }
}
