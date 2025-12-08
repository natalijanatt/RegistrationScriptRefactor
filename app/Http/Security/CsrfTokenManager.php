<?php

declare(strict_types=1);

namespace App\Http\Security;

use App\Http\Session\SessionInterface;
use Random\RandomException;

/**
 * Manages CSRF token generation and validation.
 * 
 * Tokens are generated once per session and reused until session ends.
 * This prevents issues with multiple tabs, back button, and double-submits.
 */
class CsrfTokenManager
{
    private const SESSION_KEY = '_csrf_token';
    private const TOKEN_LENGTH = 32;

    public function __construct(
        private readonly SessionInterface $session
    ) {}

    /**
     * Generate a new CSRF token and store it in the session.
     * @throws RandomException
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $this->session->set(self::SESSION_KEY, $token);

        // Force session write to ensure token is persisted
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
            session_start();
        }

        return $token;
    }

    /**
     * Get the current CSRF token, generating one if it doesn't exist.
     * @throws RandomException
     */
    public function getToken(): string
    {
        $token = $this->session->get(self::SESSION_KEY);

        if ($token === null || $token === '') {
            $token = $this->generateToken();
        }

        return $token;
    }

    /**
     * Get the stored token without generating a new one.
     */
    public function getStoredToken(): ?string
    {
        return $this->session->get(self::SESSION_KEY);
    }

    /**
     * Validate a submitted token against the stored token.
     */
    public function isValid(?string $submittedToken): bool
    {
        if ($submittedToken === null || $submittedToken === '') {
            return false;
        }

        $storedToken = $this->session->get(self::SESSION_KEY);

        if ($storedToken === null || $storedToken === '') {
            return false;
        }

        return hash_equals($storedToken, $submittedToken);
    }

    /**
     * Regenerate the CSRF token.
     * Only call this on session regeneration (login/logout), not on every form submission.
     * @throws RandomException
     */
    public function regenerateToken(): string
    {
        return $this->generateToken();
    }
}
