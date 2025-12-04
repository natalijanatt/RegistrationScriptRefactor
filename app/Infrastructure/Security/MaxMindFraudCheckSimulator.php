<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Security\FraudCheckResult;
use App\Domain\Security\FraudCheckService;

class MaxMindFraudCheckSimulator implements FraudCheckService
{
    public function __construct(
        private readonly array $blockedEmails = [],
        private readonly array $blockedIpAddresses = [],
    ) {}

    public function check(string $email, string $ipAddress): FraudCheckResult
    {
        if (in_array($email, $this->blockedEmails, true)) {
            return FraudCheckResult::fraudulent(90.0, 'Email is in blocked list');
        }

        if (in_array($ipAddress, $this->blockedIpAddresses, true)) {
            return FraudCheckResult::fraudulent(90.0, 'IP address is in blocked list');
        }

        $domain = substr(strrchr($email, '@') ?: '', 1);
        if ($domain === 'fraud-test.com') {
            return FraudCheckResult::fraudulent(85.0, 'Domain is known for fraud');
        }

        if (str_starts_with($ipAddress, '10.13.')) {
            return FraudCheckResult::fraudulent(80.0, 'IP address in suspicious range');
        }

        return FraudCheckResult::safe();
    }
}
