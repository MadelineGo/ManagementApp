<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\Orders\Commands\CreateOrderCommand;
use App\Application\Orders\Handlers\CreateOrderHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    public function store(Request $request, CreateOrderHandler $handler)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'amount' => 'required|numeric',
            'description' => 'required|string',
        ]);

        $command = new CreateOrderCommand(
            clientId: $request->input('client_id'),
            amount: $request->input('amount'),
            description: $request->input('description')
        );

        $handler->handle($command);

        return response()->json(['message' => 'Order created successfully'], 201);
    }

    public function index(Request $request, \App\Application\Orders\Handlers\ListOrdersHandler $handler)
    {
        $query = \App\Application\Orders\Queries\ListOrdersQuery::fromArray($request->all());
        $orders = $handler->handle($query);
        
        // Transform to array/json
        $data = array_map(fn($order) => [
            'id' => $order->getId(),
            'client_id' => $order->getClientId(),
            'amount' => $order->getAmount(),
            'description' => $order->getDescription(),
            'status' => $order->getStatus(),
            'created_at' => $order->getCreatedAt()?->format('Y-m-d H:i:s'),
        ], $orders);

        return response()->json($data);
    }

    public function update(Request $request, int $id, \App\Application\Orders\Handlers\UpdateOrderHandler $handler)
    {
        $command = new \App\Application\Orders\Commands\UpdateOrderCommand(
            id: $id,
            clientId: $request->input('client_id'),
            amount: $request->input('amount'),
            description: $request->input('description'),
            status: $request->input('status')
        );

        try {
            $handler->handle($command);
            return response()->json(['message' => 'Order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function destroy(int $id, \App\Application\Orders\Handlers\DeleteOrderHandler $handler)
    {
        $command = new \App\Application\Orders\Commands\DeleteOrderCommand($id);
        $handler->handle($command);

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
