<?php
declare(strict_types=1);
namespace App\QueryBuilder\Domain\Contracts;

interface SqlExpression
{
    public function toSql(SqlDialect $dialect): string;
}