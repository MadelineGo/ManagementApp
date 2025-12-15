<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Shared\UnitOfWorkInterface;
use Illuminate\Support\Facades\DB;

class EloquentUnitOfWork implements UnitOfWorkInterface
{
    public function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    public function commit(): void
    {
        DB::commit();
    }

    public function rollback(): void
    {
        DB::rollBack();
    }

    public function saveChanges(): void
    {
        // No-op: Eloquent persiste inmediatamente al llamar save(), no requiere flush explícito.
    }
}
