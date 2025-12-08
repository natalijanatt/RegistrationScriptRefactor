<?php

declare(strict_types=1);

namespace App\Application\Validation\Contracts;

/**
 * Interface for objects that have password confirmation.
 * Allows validation rules to work with any DTO that needs password matching.
 */
interface HasPasswordConfirmation extends HasPassword
{
    public function getPasswordConfirmation(): string;
}




