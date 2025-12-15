<?php

namespace App\Domain\Orders;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(int $id): ?Order;
    public function search(array $filters): array;
    public function update(Order $order): void;
    public function delete(int $id): void;

    // Statistics
    public function countAll(): int;
    public function countByStatus(string $status): int;
    /**
     * Returns array of ['month' => 'YYYY-MM', 'count' => int]
     */
    public function getMonthlyOrderCounts(): array;
}
