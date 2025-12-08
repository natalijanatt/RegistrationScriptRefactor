<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Exceptions;

/**
 * Thrown when a SQL statement fails to prepare
 */
class QueryPrepareException extends QueryException
{
    public static function fromMysqliError(string $error, string $sql): self
    {
        return new self(
            message: "Failed to prepare statement: {$error}",
            sql: $sql,
        );
    }
}





