<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\RegisterUser\RegisterUser;
use App\Application\RegisterUser\RegisterUserRequest;
use App\Domain\Logging\Logger;
use App\Http\Contracts\ViewInterface;
use App\Http\Middleware\RegistrationValidationMiddleware;
use App\Http\Request\Request;
use App\Http\Session\SessionInterface;
use App\Http\View\JsonView;

class RegisterController
{
    public function __construct(
        private readonly RegisterUser $registerUser,
        private readonly Logger $logger,
        private readonly SessionInterface $session
    ) {}

    public function handle(Request $request): ViewInterface
    {
        try {
            // Get pre-validated DTO from middleware, or create from request as fallback
            $useCaseRequest = $request->getAttribute(
                RegistrationValidationMiddleware::ATTRIBUTE_NAME
            );

            if (!$useCaseRequest instanceof RegisterUserRequest) {
                throw new \RuntimeException('RegisterController requires RegistrationValidationMiddleware');
            }

            $response = $this->registerUser->execute($useCaseRequest);

            if (!$response->success) {
                return new JsonView([
                    'success' => false,
                    'error' => $response->error,
                ]);
            }

            $this->session->set('userId', $response->userId);

            return new JsonView([
                'success' => true,
                'userId' => $response->userId,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Registration error: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return new JsonView([
                'success' => false,
                'error' => 'server_error'
            ], 500);
        }
    }
}
