<?php

declare(strict_types=1);

namespace App\Application\Validation\Rules;

use App\Application\Validation\Contracts\HasPasswordConfirmation;
use App\Application\Validation\ValidationResult;

class PasswordsMatchRule implements ValidationRuleInterface
{
    public function supports(object $input): bool
    {
        return $input instanceof HasPasswordConfirmation;
    }

    public function validate(object $input, ValidationResult $result): void
    {
        if (!$this->supports($input)) {
            return;
        }
//        REFACTORED:
//        if ($request->password !== '' && $request->passwordConfirmation !== '') {
//            if ($request->password !== $request->passwordConfirmation) {
//                $result->addError('password_mismatch');
//            }
//        }
        /** @var HasPasswordConfirmation $input */
        if ($this->passwordsDoNotMatch($input)) {
            $result->addError('password_mismatch');
        }
    }

    private function passwordsDoNotMatch(HasPasswordConfirmation $input): bool
    {
        $password = $input->getPassword();
        $confirmation = $input->getPasswordConfirmation();

        return $password !== '' 
            && $confirmation !== '' 
            && $password !== $confirmation;
    }
}
