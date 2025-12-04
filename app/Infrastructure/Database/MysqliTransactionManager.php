<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Persistence\TransactionManager;

class MysqliTransactionManager implements TransactionManager
{
    public function __construct(private readonly \mysqli $connection)
    {
    }

    public function beginTransaction(): void
    {
        $this->connection->begin_transaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollback();
    }
}
