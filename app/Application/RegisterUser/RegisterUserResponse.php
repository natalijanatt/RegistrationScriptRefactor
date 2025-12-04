<?php

declare(strict_types=1);

namespace App\Application\RegisterUser;

class RegisterUserResponse
{
    public function __construct(public bool $success, public ?int $userId=null, public ?string $error=null)
    {}
}