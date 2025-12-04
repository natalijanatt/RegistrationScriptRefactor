<?php

declare(strict_types=1);

namespace App\Domain\Security;

interface FraudCheckService
{
    /**
     * Check if the given email and IP address are flagged as fraudulent.
     *
     * @param string $email User's email address
     * @param string $ipAddress User's IP address
     * @return FraudCheckResult The result of the fraud check
     */
    public function check(string $email, string $ipAddress): FraudCheckResult;
}
