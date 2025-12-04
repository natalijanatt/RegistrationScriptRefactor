<?php

declare(strict_types=1);

namespace App\Domain\Persistence;

interface TransactionManager
{
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
}
