<?php

namespace App\Domain\Clients;

interface ClientRepositoryInterface
{
    public function save(Client $client): void;
    public function findById(int $id): ?Client;
    public function findByEmail(string $email): ?Client;
    public function add(Client $client): void;
    public function findAll(): array;
    public function delete(int $id): void;
    public function countActive(): int;
}
