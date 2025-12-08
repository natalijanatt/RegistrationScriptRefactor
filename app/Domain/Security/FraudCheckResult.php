<?php

declare(strict_types=1);

namespace App\Domain\Security;

class FraudCheckResult
{
    public function __construct(
        private readonly bool $isFraudulent,
        private readonly float $riskScore,
        private readonly string $reason = ''
    ) {}

    public function isFraudulent(): bool
    {
        return $this->isFraudulent;
    }

    public function getRiskScore(): float
    {
        return $this->riskScore;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public static function safe(): self
    {
        return new self(false, 0.0);
    }

    public static function fraudulent(float $riskScore, string $reason): self
    {
        return new self(true, $riskScore, $reason);
    }
}