<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Contracts;

interface ConnectionInterface
{
    public function getExecutor(): QueryExecutor;
    public function getDialect(): SqlDialect;
}