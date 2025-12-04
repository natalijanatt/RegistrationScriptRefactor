<?php

declare(strict_types=1);

namespace App\Application\Validation;

interface ValidatorInterface
{
    /**
     * Validates the given input object.
     *
     * @param object $input  The data to validate (DTO, command, etc.)
     * @return ValidationResult
     */
    public function validate(object $input): ValidationResult;
}
