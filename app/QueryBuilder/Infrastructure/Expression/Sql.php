<?php

declare(strict_types=1);

namespace App\QueryBuilder\Infrastructure\Expression;

use App\QueryBuilder\Domain\Contracts\SqlExpression;

final class Sql
{
    private function __construct()
    {}
    public static function raw(string $expression): SqlExpression
    {
        return new RawExpression($expression);
    }
}