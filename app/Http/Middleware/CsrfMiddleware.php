<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Contracts\MiddlewareInterface;
use App\Http\Contracts\ViewInterface;
use App\Http\Request\Request;
use App\Http\Security\CsrfTokenManager;
use App\Http\View\JsonView;

/**
 * Middleware that validates CSRF tokens on state-changing requests.
 */
class CsrfMiddleware implements MiddlewareInterface
{
    private const PROTECTED_METHODS = ['post', 'put', 'patch', 'delete'];
    private const TOKEN_FIELD = '_csrf_token';
    private const TOKEN_HEADER = 'x-csrf-token';

    public function __construct(
        private readonly CsrfTokenManager $csrfManager
    ) {}

    public function handle(Request $request, callable $next): ViewInterface
    {
        // Only validate on state-changing requests
        if (!$this->requiresValidation($request)) {
            return $next($request);
        }

        $submittedToken = $this->extractToken($request);

        if (!$this->csrfManager->isValid($submittedToken)) {
            return new JsonView([
                'success' => false,
                'error' => 'csrf_token_invalid',
            ], 403);
        }

        return $next($request);
    }

    private function requiresValidation(Request $request): bool
    {
        return in_array(strtolower($request->method()), self::PROTECTED_METHODS, true);
    }

    private function extractToken(Request $request): ?string
    {
        // Check request body first (form submission)
        $body = $request->body();
        if (isset($body[self::TOKEN_FIELD]) && is_string($body[self::TOKEN_FIELD])) {
            return $body[self::TOKEN_FIELD];
        }

        // Check header (AJAX requests)
        $headers = $request->headers();
        if (isset($headers[self::TOKEN_HEADER]) && is_string($headers[self::TOKEN_HEADER])) {
            return $headers[self::TOKEN_HEADER];
        }

        return null;
    }
}
