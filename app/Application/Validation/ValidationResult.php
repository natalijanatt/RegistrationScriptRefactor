<?php

declare(strict_types=1);

namespace App\Application\Validation;

class ValidationResult
{
    public function __construct(
        private array $errors = []
    )
    {}

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors[0] ?? null;
    }

    public function addError(string $errorCode): void
    {
        $this->errors[] = $errorCode;
    }
}