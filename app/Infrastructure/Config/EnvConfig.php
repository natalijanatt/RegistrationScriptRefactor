<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use App\Domain\Config\ConfigInterface;
use RuntimeException;

class EnvConfig implements ConfigInterface
{
    private array $config;

    public function __construct(array $env)
    {
        $this->config = $env;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->config[$key];
        }

        if ($default !== null) {
            return $default;
        }

        throw new RuntimeException("Missing required config key: {$key}");
    }

    public function getString(string $key, ?string $default = null): string
    {
        return (string) $this->get($key, $default);
    }

    public function getInt(string $key, ?int $default = null): int
    {
        return (int) $this->get($key, $default);
    }

    public function getBool(string $key, ?bool $default = null): bool
    {
        $value = $this->get($key, $default);

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default ?? false;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }
}


