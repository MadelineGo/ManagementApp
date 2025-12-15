<?php

namespace App\Domain\Orders;

class Order
{
    private ?int $id;
    private int $clientId;
    private string $description;
    private float $amount;
    private string $status;
    private ?\DateTimeImmutable $createdAt;
    public function __construct(
        int $clientId,
        float $amount,
        string $description = '',
        string $status = 'pending',
        ?\DateTimeImmutable $createdAt = null,
        ?int $id = null
    ) {
        $this->clientId = $clientId;
        $this->amount = $amount;
        $this->description = $description;
        $this->status = $status;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->id = $id;
    }

    public function markAsCompleted(): void
    {
        $this->status = 'completed';
    }

    public function markAsCancelled(): void
    {
        $this->status = 'cancelled';
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updateClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function updateAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function updateDescription(string $description): void
    {
        $this->description = $description;
    }
}
