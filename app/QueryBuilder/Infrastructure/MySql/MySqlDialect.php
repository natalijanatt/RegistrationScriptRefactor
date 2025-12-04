<?php

declare(strict_types=1);

namespace App\QueryBuilder\Infrastructure\MySql;

use App\QueryBuilder\Domain\Contracts\SqlDialect;

class MySqlDialect implements SqlDialect
{
    public function quoteIdentifier(string $name): string
    {
        $escaped = str_replace('`', '``', $name);

        return sprintf('`%s`', $escaped);
    }

    public function compileLimitOffset(?int $limit, ?int $offset): string
    {
        if ($limit === null && $offset === null) {
            return '';
        }

        if ($limit !== null && $offset === null) {
            return sprintf(' LIMIT %d', $limit);
        }

        if ($limit === null && $offset !== null) {
            return sprintf(' LIMIT 18446744073709551615 OFFSET %d', $offset);
        }

        return sprintf(' LIMIT %d OFFSET %d', $limit, $offset);
    }

    public function placeholder(int $index): string
    {
        return '?';
    }
}