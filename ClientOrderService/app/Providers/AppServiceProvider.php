<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Clients\ClientRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\EloquentClientRepository;
use App\Domain\Orders\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\EloquentOrderRepository;
use App\Domain\Shared\UnitOfWorkInterface;
use App\Infrastructure\Persistence\EloquentUnitOfWork;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 1. Mapear las interfaces de Dominio a las implementaciones de Infraestructura (Bindings)

        $this->app->bind(
            ClientRepositoryInterface::class,
            EloquentClientRepository::class
        );

        $this->app->bind(
            OrderRepositoryInterface::class,
            EloquentOrderRepository::class
        );

        $this->app->bind(
            UnitOfWorkInterface::class,
            EloquentUnitOfWork::class
        );

        // 2. Mapear el Handler
        // El Container de Laravel ya puede resolver el Handler si sus dependencias están resueltas.
        // $this->app->bind(CreateClientHandler::class, function ($app) { ... }); // Opcional
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
