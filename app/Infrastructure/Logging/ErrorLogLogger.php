<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Domain\Logging\Logger;

/**
 * Logger implementation using PHP's error_log()
 */
class ErrorLogLogger implements Logger
{
    public function emergency(string $message, array $context = []): void
    {
        $this->log('EMERGENCY', $message, $context);
    }
    
    public function alert(string $message, array $context = []): void
    {
        $this->log('ALERT', $message, $context);
    }
    
    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }
    
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }
    
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }
    
    public function notice(string $message, array $context = []): void
    {
        $this->log('NOTICE', $message, $context);
    }
    
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }
    
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }
    
    private function log(string $level, string $message, array $context): void
    {
        $interpolated = $this->interpolate($message, $context);
        $formatted = sprintf('[%s] %s', $level, $interpolated);
        
        if (!empty($context['exception']) && $context['exception'] instanceof \Throwable) {
            $formatted .= "\nStack trace: " . $context['exception']->getTraceAsString();
        }
        
        error_log($formatted);
    }
    
    /**
     * Interpolate context values into message placeholders
     * e.g., "User {email} registered" with ['email' => 'test@example.com']
     */
    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if ($key === 'exception') {
                continue;
            }
            if (is_string($val) || (is_object($val) && method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = (string) $val;
            }
        }
        
        return strtr($message, $replace);
    }
}

