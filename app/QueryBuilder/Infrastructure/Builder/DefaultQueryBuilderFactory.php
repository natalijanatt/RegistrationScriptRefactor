<?php
declare(strict_types=1);
namespace App\QueryBuilder\Infrastructure\Builder;

use App\QueryBuilder\Domain\Contracts\ConnectionInterface;
use App\QueryBuilder\Domain\Contracts\QueryBuilderFactoryInterface;
use App\QueryBuilder\Domain\Contracts\QueryBuilderInterface;

class DefaultQueryBuilderFactory implements QueryBuilderFactoryInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection
    )
    {}

    public function make(): QueryBuilderInterface
    {
        return new QueryBuilder(
            $this->connection->getDialect(),
            $this->connection->getExecutor()
        );
    }
}