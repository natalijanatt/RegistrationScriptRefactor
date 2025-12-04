<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Security\FraudCheckService;
use App\Domain\Security\FraudCheckResult;

/**
 * Simulated MaxMind fraud detection service.
 * 
 * This is a simulation that flags certain patterns as fraudulent:
 * - Emails containing "fraud", "spam", "fake", "test123"
 * - Disposable email domains
 * - IP addresses in certain "suspicious" ranges
 * - Deterministic "random" flagging based on email hash (10% of emails)
 */
class SimulatedMaxMindService implements FraudCheckService
{
    /**
     * Disposable email domains commonly used for fraud.
     */
    private const DISPOSABLE_DOMAINS = [
        'tempmail.com',
        'throwaway.com',
        'mailinator.com',
        'guerrillamail.com',
        'fakeinbox.com',
        '10minutemail.com',
        'trashmail.com',
    ];

    /**
     * Suspicious IP ranges (simulated).
     * In reality, MaxMind uses geolocation and reputation data.
     */
    private const SUSPICIOUS_IP_PATTERNS = [
        '10.0.0.',      // Simulated "known bad" range
        '192.168.100.', // Simulated "proxy" range
        '0.0.0.0',      // Invalid IP
    ];

    /**
     * Keywords in email that indicate potential fraud.
     */
    private const SUSPICIOUS_EMAIL_KEYWORDS = [
        'fraud',
        'spam',
        'fake',
        'test123',
        'hacker',
        'scam',
    ];

    public function check(string $email, string $ipAddress): FraudCheckResult
    {
        // Check 1: Suspicious email keywords
        $emailLower = strtolower($email);
        foreach (self::SUSPICIOUS_EMAIL_KEYWORDS as $keyword) {
            if (str_contains($emailLower, $keyword)) {
                return FraudCheckResult::fraudulent(
                    85.0,
                    "Email contains suspicious keyword: {$keyword}"
                );
            }
        }

        // Check 2: Disposable email domains
        $domain = $this->extractDomain($email);
        if (in_array($domain, self::DISPOSABLE_DOMAINS, true)) {
            return FraudCheckResult::fraudulent(
                75.0,
                "Disposable email domain detected: {$domain}"
            );
        }

        // Check 3: Suspicious IP patterns
        foreach (self::SUSPICIOUS_IP_PATTERNS as $pattern) {
            if (str_starts_with($ipAddress, $pattern)) {
                return FraudCheckResult::fraudulent(
                    90.0,
                    "IP address in suspicious range: {$pattern}*"
                );
            }
        }

        // Check 4: Deterministic "random" flagging based on email hash
        // This simulates MaxMind's reputation database
        // ~10% of emails will be flagged as "known fraudulent"
        $hash = crc32($email);
        if ($hash % 10 === 0) {
            return FraudCheckResult::fraudulent(
                65.0,
                "Email found in fraud database (simulated)"
            );
        }

        // All checks passed
        return FraudCheckResult::safe();
    }

    private function extractDomain(string $email): string
    {
        $parts = explode('@', $email);
        return strtolower($parts[1] ?? '');
    }
}

