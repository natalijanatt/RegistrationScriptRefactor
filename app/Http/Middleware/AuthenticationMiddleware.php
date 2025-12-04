<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\User\UserRepository;
use App\Http\Contracts\MiddlewareInterface;
use App\Http\Contracts\ViewInterface;
use App\Http\Request\AuthenticatedRequest;
use App\Http\Request\Request;
use App\Http\Session\SessionInterface;
use App\Http\View\RedirectView;

class AuthenticationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SessionInterface $session,
    ) {}

    public function handle(Request $request, callable $next): ViewInterface
    {
        $userId = $this->session->get('userId', null);

        if ($userId === null) {
            return new RedirectView('/registration');
        }

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            $this->session->remove('userId');

            return new RedirectView('/registration');
        }

        $authenticatedRequest = new AuthenticatedRequest($user, $request);

        return $next($authenticatedRequest);
    }
}