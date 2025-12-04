<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Logging\Logger;
use App\Domain\Security\FraudCheckService;
use App\Http\Contracts\MiddlewareInterface;
use App\Http\Contracts\ViewInterface;
use App\Http\Request\Request;
use App\Http\View\JsonView;

class FraudCheckMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly FraudCheckService $fraudCheckService,
        private readonly Logger $logger
    ) {}

    public function handle(Request $request, callable $next): ViewInterface
    {
        $body = $request->body();
        $email = $body['email'] ?? '';
        $ipAddress = $request->clientIp();

        if (empty($email)) {
            return $next($request);
        }

        $result = $this->fraudCheckService->check($email, $ipAddress);

        if ($result->isFraudulent()) {
            $this->logger->warning('Fraud detected during registration', [
                'email' => $email,
                'ip' => $ipAddress,
                'risk_score' => $result->getRiskScore(),
                'reason' => $result->getReason(),
            ]);

            return new JsonView([
                'success' => false,
                'error' => 'fraud_detected',
            ], 403);
        }

        return $next($request);
    }
}

