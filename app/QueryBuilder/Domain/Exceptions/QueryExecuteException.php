<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Exceptions;

/**
 * Thrown when a prepared statement fails to execute
 */
class QueryExecuteException extends QueryException
{
    public static function fromMysqliError(string $error, string $sql, array $params = []): self
    {
        return new self(
            message: "Failed to execute statement: {$error}",
            sql: $sql,
            params: $params,
        );
    }
}





