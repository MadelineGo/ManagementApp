<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\Clients\Commands\CreateClientCommand;
use App\Application\Clients\Handlers\CreateClientHandler;
use App\Application\Clients\Queries\ListClientsQuery;
use App\Application\Clients\Handlers\ListClientsHandler;
use App\Application\Clients\Commands\UpdateClientCommand;
use App\Application\Clients\Handlers\UpdateClientHandler;
use App\Application\Clients\Commands\DeleteClientCommand;
use App\Application\Clients\Handlers\DeleteClientHandler;
use App\Infrastructure\Persistence\Eloquent\ClientModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{

    public function create(Request $request, CreateClientHandler $createClientHandler): JsonResponse
    {
        // 1. **Validación de Datos de Entrada (HTTP)**
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique(ClientModel::class, 'email')],
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
        ]);

        // 2. Obtener el ID del usuario autenticado (Inyectado por el Middleware JWT)
        $authenticatedUserId = $request->attributes->get('user_id');

        // 3. Mapear DTO a COMMAND
        $command = new CreateClientCommand(
            $request->input('name'),
            $request->input('last_name'),
            $request->input('email'),
            $request->input('address'),
            $request->input('phone_number'),
            $authenticatedUserId
        );

        try {
            // 4. Ejecutar el Caso de Uso (Llama al Handler que usa UoW y Repositorio)
            $newClient = $createClientHandler->handle($command);

            // 5. Respuesta al Cliente (Frontend Angular)
            return new JsonResponse([
                'id' => $newClient->getId(),
                'name' => $newClient->name(),
                'last_name' => $newClient->lastName(),
                'email' => $newClient->email(),
                'phone_number' => $newClient->phone(),
                'message' => 'Client created successfully.'
            ], 201); // 201 Created

        } catch (\Exception $e) {
            // Captura errores de unicidad o fallas de transacción/lógica del Handler
            $statusCode = ($e->getMessage() === 'El cliente con este correo electrónico ya existe.') ? 409 : 500;
            return new JsonResponse(['error' => $e->getMessage()], $statusCode);
        }
    }

    public function index(ListClientsHandler $handler): JsonResponse
    {
        $query = new ListClientsQuery();
        $clients = $handler->handle($query);

        $data = array_map(function ($client) {
            return [
                'id' => $client->getId(),
                'name' => $client->name(),
                'last_name' => $client->lastName(),
                'email' => $client->email(),
                'address' => $client->address(),
                'phone_number' => $client->phone(),
                'isActive' => $client->isActive(),
            ];
        }, $clients);

        return new JsonResponse($data);
    }

    public function update(int $id, Request $request, UpdateClientHandler $handler): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique(ClientModel::class, 'email')->ignore($id, 'id')],
            'address' => 'nullable|string',
        ]);

        $command = new UpdateClientCommand(
            $id,
            $request->input('name'),
            $request->input('email'),
            $request->input('address')
        );

        try {
            $handler->handle($command);
            return new JsonResponse(['message' => 'Client updated successfully.']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id, DeleteClientHandler $handler): JsonResponse
    {
        $command = new DeleteClientCommand($id);
        $handler->handle($command);

        return new JsonResponse(['message' => 'Client deleted successfully.']);
    }
}
