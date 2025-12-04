<?php

declare(strict_types=1);

namespace App\Application\Validation\Contracts;

/**
 * Interface for objects that have a password field.
 * Allows validation rules to work with any DTO that provides password.
 */
interface HasPassword
{
    public function getPassword(): string;
}

