<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Application\RegisterUser\RegisterUserRequest;
use App\Application\RegisterUser\RegisterUserValidator;
use App\Http\Contracts\MiddlewareInterface;
use App\Http\Contracts\ViewInterface;
use App\Http\Request\Request;
use App\Http\View\JsonView;

class RegistrationValidationMiddleware implements MiddlewareInterface
{
    public const ATTRIBUTE_NAME = 'registerUserRequest';

    public function __construct(
        private readonly RegisterUserValidator $validator
    ) {}

    public function handle(Request $request, callable $next): ViewInterface
    {
        // Create the DTO from request data
        $body = $request->body();
        
        $dto = new RegisterUserRequest(
            email: $body['email'] ?? '',
            password: $body['password'] ?? '',
            passwordConfirmation: $body['password2'] ?? '',
            ipAddress: $request->clientIp()
        );

        // Validate
        $validationResult = $this->validator->validate($dto);

        if (!$validationResult->isValid()) {
            return new JsonView([
                'success' => false,
                'error' => $validationResult->firstError(),
                'errors' => $validationResult->getErrors(),
            ], 422);
        }

        // Store validated DTO in request for controller access
        $validatedRequest = $request->withAttribute(self::ATTRIBUTE_NAME, $dto);

        return $next($validatedRequest);
    }
}