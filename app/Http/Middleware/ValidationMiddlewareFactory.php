<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Application\Validation\ValidatorInterface;
use App\Http\Request\Request;

/**
 * Factory for creating ValidationMiddleware instances with specific validators.
 * 
 */
class ValidationMiddlewareFactory
{
    /**
     *
     * @param ValidatorInterface $validator The validator to use
     * @param callable(Request): object $dtoFactory Creates DTO from request
     * @param string $attributeName Name to store validated DTO under
     */
    public function create(
        ValidatorInterface $validator,
        callable $dtoFactory,
        string $attributeName = 'validatedData'
    ): ValidationMiddleware {
        return new ValidationMiddleware($validator, $dtoFactory, $attributeName);
    }
}

