<?php

declare(strict_types=1);

namespace App\Http\Request;

use App\Domain\User\User;

/**
 * A Request that is guaranteed to have an authenticated user.
 * Use this for type-safety in controllers that require authentication.
 */
class AuthenticatedRequest extends Request
{
    public function __construct(User $user, Request $base)
    {
        parent::__construct(
            $base->method(),
            $base->uri(),
            $base->query(),
            $base->body(),
            $base->headers(),
            $base->cookies(),
            $base->files(),
            $base->rawBody(),
            $base->clientIp(),
            $user,
            $base->getAttributes(),
        );
    }

    /**
     * Returns the authenticated user (guaranteed non-null).
     */
    public function user(): User
    {
        return parent::user();
    }
}
