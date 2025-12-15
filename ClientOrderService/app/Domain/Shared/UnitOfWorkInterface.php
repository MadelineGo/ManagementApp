<?php

namespace App\Domain\Shared;

interface UnitOfWorkInterface
{
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
    public function saveChanges(): void;
}
