<?php

declare(strict_types=1);

namespace App\Application\Validation\Contracts;

/**
 * Interface for objects that have an email field.
 * Allows validation rules to work with any DTO that provides email.
 */
interface HasEmail
{
    public function getEmail(): string;
}




