<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Config\ConfigInterface;
use RuntimeException;

class DatabaseConnection
{
    private \mysqli $connection;

    public function __construct(string $host, string $username, string $password, string $database)
    {
        $this->connection = new \mysqli($host, $username, $password, $database);

        if ($this->connection->connect_error) {
            throw new RuntimeException('DB connection failed: ' . $this->connection->connect_error);
        }

        $this->connection->set_charset('utf8mb4');
    }

    public function getConnection(): \mysqli
    {
        return $this->connection;
    }

    public static function fromConfig(ConfigInterface $config): self
    {
        return new self(
            $config->getString('DB_HOST', 'db'),
            $config->getString('DB_USER', 'root'),
            $config->getString('DB_PASSWORD', ''),
            $config->getString('DB_DATABASE', '')
        );
    }

    public function __destruct()
    {
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }
}
