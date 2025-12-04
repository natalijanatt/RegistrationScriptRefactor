<?php

declare(strict_types=1);

namespace App\Application\Validation\Rules;

use App\Application\Validation\Contracts\HasEmail;
use App\Application\Validation\ValidationResult;

class EmailRequiredRule implements ValidationRuleInterface
{
    public function supports(object $input): bool
    {
        return $input instanceof HasEmail;
    }

    public function validate(object $input, ValidationResult $result): void
    {
        if (!$this->supports($input)) {
            return;
        }

        /** @var HasEmail $input */
        if (empty($input->getEmail())) {
            $result->addError('email');
        }
    }
}
