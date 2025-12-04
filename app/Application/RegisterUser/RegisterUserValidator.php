<?php

declare(strict_types=1);

namespace App\Application\RegisterUser;

use App\Application\Validation\Rules\ValidationRuleInterface;
use App\Application\Validation\ValidatorInterface;
use App\Application\Validation\ValidationResult;

class RegisterUserValidator implements ValidatorInterface
{
    /** @var ValidationRuleInterface[] */
    private array $rules;

    public function __construct(ValidationRuleInterface ...$rules)
    {
        $this->rules = $rules;
    }

    public function validate(object $input): ValidationResult
    {
        $result = new ValidationResult();

        foreach ($this->rules as $rule) {
            if ($rule->supports($input)) {
                $rule->validate($input, $result);
            }
        }

        return $result;
    }
}
