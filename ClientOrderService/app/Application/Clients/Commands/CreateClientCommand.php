<?php

namespace App\Application\Clients\Commands;

class CreateClientCommand
{
    public readonly string $name;
    public readonly string $lastName;
    public readonly string $email;
    public readonly ?string $address;
    public readonly ?string $phoneNumber;
    public readonly ?string $authenticatedUserId;

    public function __construct(string $name, string $lastName, string $email, ?string $address, ?string $phoneNumber, ?string $authenticatedUserId = null)
    {
        // Validación básica
        if (empty($name) || empty($lastName) || empty($email)) {
            throw new \InvalidArgumentException("Name, last name and email are required for the command.");
        }

        $this->name = $name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
        $this->authenticatedUserId = $authenticatedUserId;
    }
}
