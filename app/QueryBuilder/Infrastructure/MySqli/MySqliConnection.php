<?php

declare(strict_types=1);

namespace App\QueryBuilder\Infrastructure\MySqli;

use App\QueryBuilder\Domain\Contracts\ConnectionInterface;
use App\QueryBuilder\Domain\Contracts\QueryExecutor;
use App\QueryBuilder\Domain\Contracts\SqlDialect;
use App\QueryBuilder\Infrastructure\MySql\MySqlDialect;
use mysqli;

class MySqliConnection implements ConnectionInterface
{
    private readonly QueryExecutor $executor;
    private readonly SqlDialect $dialect;

    public function __construct(
        private readonly mysqli $mysqli,
        ?SqlDialect $dialect = null,
        ?QueryExecutor $executor = null
    ) {
        $this->dialect  = $dialect  ?? new MySqlDialect();
        $this->executor = $executor ?? new MySqliQueryExecutor($mysqli);
    }

    public function getExecutor(): QueryExecutor
    {
        return $this->executor;
    }

    public function getDialect(): SqlDialect
    {
        return $this->dialect;
    }

    public function getMysqli(): mysqli
    {
        return $this->mysqli;
    }

}