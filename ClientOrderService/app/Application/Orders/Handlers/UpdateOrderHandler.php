<?php

namespace App\Application\Orders\Handlers;

use App\Application\Orders\Commands\UpdateOrderCommand;
use App\Domain\Orders\OrderRepositoryInterface;
use Exception;

class UpdateOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $repository
    ) {}

    public function handle(UpdateOrderCommand $command): void
    {
        $order = $this->repository->findById($command->id);

        if (!$order) {
            throw new Exception("Order not found");
        }

        if ($command->status === 'completed') {
            $order->markAsCompleted();
        } elseif ($command->status === 'cancelled') {
            $order->markAsCancelled();
        } else {
            // Only update fields if provided. 
            // Since we are using Constructor for Order state (immutable-ish), we might need setters or create new instance logic 
            // if the Entity implies immutability. But looking at 'Order.php', properties are private but no setters?
            // Wait, I didn't add Setters in Order.php! 
            // The previous implementation used public properties: $order->items = ...
            // Now they are private. I need to add update methods or setters to Order.php or use Reflection (bad).
            // I should add an 'updateDetails' method to Order Entity.
            
            // For now, I'll assume I can add a method to Order. Let's make this Handler call a method on Order.
            // But since I can't edit Order.php in this tool call (sequential), I will assume I will add `updateDetails` to Order.php next.
            // Or I can add setters. The previous code did `$order->customerId = ...`.
            
            // Let's defer this specific logic change until I update Order.php with setters/update method.
            // Actually, I can't leave broken code.
            // I will implement the calls to hypothetical setters/methods and then update Order.php immediately after.
             if ($command->clientId) $order->updateClientId($command->clientId);
             if ($command->amount) $order->updateAmount($command->amount);
             if ($command->description) $order->updateDescription($command->description);
        }

        $this->repository->update($order);
    }
}
