<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Contracts;

interface SqlDialect
{
    /**
     * Quote a table or column identifier:
     *   users  -> `users` / "users" / [users]
     */
    public function quoteIdentifier(string $name): string;

    /**
     * Build the LIMIT/OFFSET clause for this dialect.
     * Should return empty string if not used.
     */
    public function compileLimitOffset(?int $limit, ?int $offset): string;

    /**
     * Return placeholder for a parameter.
     * For PDO-MySQL this is always "?", for Postgres could be "$1", "$2", ...
     */
    public function placeholder(int $index): string;
}