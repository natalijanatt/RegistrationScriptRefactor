<?php

declare(strict_types=1);

namespace App\QueryBuilder\Infrastructure\Expression;

use App\QueryBuilder\Domain\Contracts\SqlDialect;
use App\QueryBuilder\Domain\Contracts\SqlExpression;

final class RawExpression implements SqlExpression
{
    public function __construct(
        private readonly string $expression
    )
    {}

    public function toSql(SqlDialect $dialect): string
    {
        return $this->expression;
    }
}