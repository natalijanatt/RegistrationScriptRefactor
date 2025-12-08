<?php

declare(strict_types=1);

namespace App\Domain\Config;

use RuntimeException;

/**
 * Configuration access interface.
 * 
 * Provides typed access to application configuration values.
 */
interface ConfigInterface
{
    /**
     * Get a configuration value.
     *
     * @param string $key The configuration key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed The configuration value
     * @throws RuntimeException If key doesn't exist and no default provided
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Get a string configuration value.
     *
     * @param string $key The configuration key
     * @param string|null $default Default value if key doesn't exist
     * @return string The configuration value as string
     */
    public function getString(string $key, ?string $default = null): string;

    /**
     * Get an integer configuration value.
     *
     * @param string $key The configuration key
     * @param int|null $default Default value if key doesn't exist
     * @return int The configuration value as integer
     */
    public function getInt(string $key, ?int $default = null): int;

    /**
     * Get a boolean configuration value.
     *
     * @param string $key The configuration key
     * @param bool|null $default Default value if key doesn't exist
     * @return bool The configuration value as boolean
     */
    public function getBool(string $key, ?bool $default = null): bool;

    /**
     * Check if a configuration key exists.
     *
     * @param string $key The configuration key
     * @return bool True if key exists
     */
    public function has(string $key): bool;
}