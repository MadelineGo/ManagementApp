<?php

namespace App\Application\Orders\Queries;

class ListOrdersQuery
{
    public function __construct(
        public ?string $status = null,
        public ?int $clientId = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'] ?? null,
            clientId: $data['client_id'] ?? null,
            dateFrom: $data['date_from'] ?? null,
            dateTo: $data['date_to'] ?? null
        );
    }
}
