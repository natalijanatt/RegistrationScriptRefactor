<?php

declare(strict_types=1);

namespace App\Application\Validation\Rules;

use App\Application\Validation\ValidationResult;

/**
 * Generic validation rule interface.
 * 
 * Rules can validate any object type and should check if they support
 * the given input before performing validation.
 */
interface ValidationRuleInterface
{
    /**
     * Check if this rule can validate the given input.
     */
    public function supports(object $input): bool;

    /**
     * Validate the input and add any errors to the result.
     * 
     * @param object $input The object to validate
     * @param ValidationResult $result The result to add errors to
     */
    public function validate(object $input, ValidationResult $result): void;
}
