<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Application\Validation\ValidatorInterface;
use App\Http\Contracts\MiddlewareInterface;
use App\Http\Contracts\ViewInterface;
use App\Http\Request\Request;
use App\Http\View\JsonView;

/**
 * Generic validation middleware that validates request data using a provided validator.
 * 
 * This middleware:
 * 1. Extracts data from the request
 * 2. Transforms it into a DTO using the provided factory
 * 3. Validates the DTO
 * 4. Returns validation errors or passes validated request to the next handler
 */
class ValidationMiddleware implements MiddlewareInterface
{
    /**
     * @param ValidatorInterface $validator The validator to use
     * @param callable(Request): object $dtoFactory Factory that creates DTO from request
     * @param string $requestAttribute Attribute name to store validated DTO in request
     */
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly mixed $dtoFactory,
        private readonly string $requestAttribute = 'validatedData'
    ) {}

    public function handle(Request $request, callable $next): ViewInterface
    {
        // Create DTO from request using the factory
        $dto = ($this->dtoFactory)($request);
        
        // Validate the DTO
        $validationResult = $this->validator->validate($dto);
        
        if (!$validationResult->isValid()) {
            return new JsonView([
                'success' => false,
                'error' => $validationResult->firstError(),
                'errors' => $validationResult->getErrors(),
            ], 422);
        }
        
        // Store validated DTO in request for controller access
        $validatedRequest = $request->withAttribute($this->requestAttribute, $dto);
        
        return $next($validatedRequest);
    }
}




