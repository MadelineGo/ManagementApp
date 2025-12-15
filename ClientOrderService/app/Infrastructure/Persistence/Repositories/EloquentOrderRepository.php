<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\OrderModel;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function save(Order $order): void
    {
        OrderModel::create([
            'client_id' => $order->getClientId(),
            'description' => $order->getDescription(),
            'amount' => $order->getAmount(),
            'status' => $order->getStatus(),
        ]);
    }

    public function findById(int $id): ?Order
    {
        $model = OrderModel::find($id);
        if (!$model) {
            return null;
        }
        return $this->toDomain($model);
    }

    public function search(array $filters): array
    {
        $query = OrderModel::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
        
        // Add more filters as needed

        return $query->get()->map(fn($model) => $this->toDomain($model))->toArray();
    }

    public function update(Order $order): void
    {
        $model = OrderModel::find($order->getId());
        if ($model) {
            $model->update([
                'client_id' => $order->getClientId(),
                'description' => $order->getDescription(),
                'amount' => $order->getAmount(),
                'status' => $order->getStatus(),
            ]);
        }
    }

    public function delete(int $id): void
    {
        OrderModel::destroy($id);
    }

    public function countAll(): int
    {
        return OrderModel::count();
    }

    public function countByStatus(string $status): int
    {
        return OrderModel::where('status', $status)->count();
    }

    public function getMonthlyOrderCounts(): array
    {
        // Determine DB driver to use correct date function
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            $expression = "strftime('%Y-%m', created_at)";
        } else {
            // Assume SQL Server (sqlsrv) or MySQL
            // SQL Server: FORMAT(created_at, 'yyyy-MM')
            // MySQL: DATE_FORMAT(created_at, '%Y-%m')
            
            if ($driver === 'sqlsrv') {
                // FORMAT() requires CLR enabled. Using standard T-SQL instead:
                // Style 120 = yyyy-mm-dd hh:mi:ss(24h)
                $expression = "LEFT(CONVERT(varchar, created_at, 120), 7)";
            } else {
                $expression = "DATE_FORMAT(created_at, '%Y-%m')"; // Default fallback to MySQL
            }
        }

        return OrderModel::selectRaw("$expression as month, count(*) as count")
            ->groupBy(\Illuminate\Support\Facades\DB::raw($expression))
            ->orderBy('month', 'desc')
            ->get()
            ->map(fn($item) => ['month' => $item->month, 'count' => (int)$item->count])
            ->toArray();
    }

    private function toDomain(OrderModel $model): Order
    {
        return new Order(
            clientId: $model->client_id,
            amount: $model->amount,
            description: $model->description,
            status: $model->status,
            createdAt: \DateTimeImmutable::createFromMutable($model->created_at),
            id: $model->id
        );
    }
}
