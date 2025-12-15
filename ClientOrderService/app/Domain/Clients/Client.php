<?php

namespace App\Domain\Clients;

class Client
{
    private ?int $id;
    private string $name;
    private string $lastName;
    private string $email;
    private ?string $address;
    private ?string $phoneNumber;
    private bool $isActive = true;
    private ?string $createdAt;

    private function __construct(?int $id, string $name, string $lastName, string $email, ?string $address, ?string $phoneNumber, bool $isActive = true, ?string $createdAt = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');
    }

    public static function create(string $name, string $lastName, string $email, ?string $address = null, ?string $phoneNumber = null): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format.");
        }

        return new self(
            null,
            $name,
            $lastName,
            $email,
            $address,
            $phoneNumber
        );
    }

    public static function fromStorage(int $id, string $name, string $lastName, string $email, ?string $address = null, ?string $phoneNumber = null, bool $isActive = true, ?string $createdAt = null): self
    {
        return new self($id, $name, $lastName, $email, $address, $phoneNumber, $isActive, $createdAt);
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function name(): string
    {
        return $this->name;
    }
    public function lastName(): string
    {
        return $this->lastName;
    }
    public function email(): string
    {
        return $this->email;
    }
    public function address(): ?string
    {
        return $this->address;
    }
    public function phone(): ?string
    {
        return $this->phoneNumber;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function update(string $name, string $email, ?string $address): void
    {
        $this->name = $name;
        $this->email = $email;
        $this->address = $address;
    }
}
