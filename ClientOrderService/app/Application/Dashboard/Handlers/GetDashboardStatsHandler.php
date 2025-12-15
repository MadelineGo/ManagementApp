<?php

namespace App\Application\Dashboard\Handlers;

use App\Application\Dashboard\Queries\GetDashboardStatsQuery;
use App\Domain\Consumers\ConsumerRepositoryInterface; // Typo protection: check domain
use App\Domain\Clients\ClientRepositoryInterface;
use App\Domain\Orders\OrderRepositoryInterface;

class GetDashboardStatsHandler
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function handle(GetDashboardStatsQuery $query): array
    {
        return [
            'total_orders' => $this->orderRepository->countAll(),
            'orders_by_status' => [
                'completed' => $this->orderRepository->countByStatus('completed'),
                'pending' => $this->orderRepository->countByStatus('pending'),
                'cancelled' => $this->orderRepository->countByStatus('cancelled'),
            ],
            'active_clients' => $this->clientRepository->countActive(),
            'monthly_activity' => $this->orderRepository->getMonthlyOrderCounts(),
        ];
    }
}
