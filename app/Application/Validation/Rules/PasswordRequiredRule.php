<?php

declare(strict_types=1);

namespace App\Application\Validation\Rules;

use App\Application\Validation\Contracts\HasPassword;
use App\Application\Validation\ValidationResult;

class PasswordRequiredRule implements ValidationRuleInterface
{
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
        if (empty($input->getPassword())) {
            $result->addError('password_required');
        }
    }
}
