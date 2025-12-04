<?php

declare(strict_types=1);

namespace App\Application\Validation\Rules;

use App\Application\Validation\Contracts\HasPassword;
use App\Application\Validation\ValidationResult;

class PasswordLengthRule implements ValidationRuleInterface
{
    public function __construct(
        private readonly int $minLength = 8
    ) {}

    public function supports(object $input): bool
    {
        return $input instanceof HasPassword;
    }

    public function validate(object $input, ValidationResult $result): void
    {
        if (!$this->supports($input)) {
            return;
        }

        /** @var HasPassword $input */
        if (!empty($input->getPassword()) && mb_strlen($input->getPassword()) < $this->minLength) {
            $result->addError('password');
        }
    }
}
