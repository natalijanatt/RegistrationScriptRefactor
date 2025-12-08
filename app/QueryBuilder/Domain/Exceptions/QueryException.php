<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Exceptions;

/**
 * Base exception for all query-related errors
 */
class QueryException extends \RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?string $sql = null,
        public readonly array $params = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}





