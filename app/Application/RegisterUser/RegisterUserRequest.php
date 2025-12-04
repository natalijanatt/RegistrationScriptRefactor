<?php

declare(strict_types=1);

namespace App\Application\RegisterUser;

use App\Application\Validation\Contracts\HasEmail;
use App\Application\Validation\Contracts\HasPasswordConfirmation;

class RegisterUserRequest implements HasEmail, HasPasswordConfirmation
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $passwordConfirmation,
        public readonly string $ipAddress = '0.0.0.0'
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPasswordConfirmation(): string
    {
        return $this->passwordConfirmation;
    }
}
