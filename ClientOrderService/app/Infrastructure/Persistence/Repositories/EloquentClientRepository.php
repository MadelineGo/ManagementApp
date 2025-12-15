<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Clients\Client;
use App\Domain\Clients\ClientRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\ClientModel;

class EloquentClientRepository implements ClientRepositoryInterface
{
    public function save(Client $client): void
    {
        $model = $client->getId() ? ClientModel::find($client->getId()) : new ClientModel();

        $model->name = $client->name();
        $model->last_name = $client->lastName();
        $model->email = $client->email();
        $model->address = $client->address();
        $model->phone_number = $client->phone();
        $model->is_active = $client->isActive();

        if ($client->getCreatedAt()) {
            $model->created_at = $client->getCreatedAt();
        }

        $model->save();

        if (!$client->getId()) {
            $client->setId($model->id);
        }
    }

    public function findById(int $id): ?Client
    {
        $model = ClientModel::find($id);
        if (!$model) return null;

        return $this->toDomain($model);
    }

    public function findByEmail(string $email): ?Client
    {
        $model = ClientModel::where('email', $email)->first();
        if (!$model) return null;

        return $this->toDomain($model);
    }

    public function add(Client $client): void
    {
        $this->save($client);
    }

    public function findAll(): array
    {
        $models = ClientModel::all();
        $clients = [];

        foreach ($models as $model) {
            $clients[] = $this->toDomain($model);
        }

        return $clients;
    }

    private function toDomain(ClientModel $model): Client
    {
        return Client::fromStorage(
            $model->id,
            $model->name,
            $model->last_name ?? '', 
            $model->email,
            $model->address,
            $model->phone_number,
            (bool) $model->is_active,
            $model->created_at?->format('Y-m-d H:i:s')
        );
    }

    public function delete(int $id): void
    {
        ClientModel::destroy($id);
    }

    public function countActive(): int
    {
        return ClientModel::where('is_active', true)->count();
    }
}
