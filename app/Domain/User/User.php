<?php

declare(strict_types=1);

namespace App\Domain\User;

class User
{
    public function __construct(private ?int $id, private string $email, private string $passwordHash)
    {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
}