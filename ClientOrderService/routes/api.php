<?php

use Illuminate\Support\Facades\Route;
use App\Infrastructure\Http\Controllers\ClientController;
use App\Infrastructure\Http\Controllers\OrderController;
use App\Infrastructure\Middleware\JwtAuthMiddleware;

// Grupo de rutas que requieren un JWT válido (Protección de rutas privadas)
Route::middleware([JwtAuthMiddleware::class])->group(function () {

    // POST /api/clients (Crear Cliente)
    Route::post('/clients', [ClientController::class, 'create']);

    // GET /api/clients (Listar Clientes)
    Route::get('/clients', [ClientController::class, 'index']);

    // PUT /api/clients/{id} (Editar Cliente)
    Route::put('/clients/{id}', [ClientController::class, 'update']);

    // DELETE /api/clients/{id} (Eliminar Cliente)
    Route::delete('/clients/{id}', [ClientController::class, 'destroy']);

    // POST /api/orders (Crear Pedido)
    Route::post('/orders', [OrderController::class, 'store']);

    // GET /api/orders (Listar Pedidos)
    Route::get('/orders', [OrderController::class, 'index']);

    // PUT /api/orders/{id} (Editar Pedido)
    Route::put('/orders/{id}', [OrderController::class, 'update']);

    // DELETE /api/orders/{id} (Eliminar Pedido)
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    // GET /api/dashboard/stats (Estadísticas)
    Route::get('/dashboard/stats', [\App\Infrastructure\Http\Controllers\DashboardController::class, 'stats']);
});
