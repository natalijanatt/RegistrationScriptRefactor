<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Exceptions;

/**
 * Thrown when QueryBuilder is used incorrectly (e.g., missing table)
 */
class QueryBuilderException extends \LogicException
{
    public static function missingTable(): self
    {
        return new self('QueryBuilder: table() must be called before building the query.');
    }
    
    public static function missingColumns(): self
    {
        return new self('QueryBuilder: No columns specified for the query.');
    }
    
    public static function invalidOperation(string $operation): self
    {
        return new self("QueryBuilder: Invalid operation '{$operation}'.");
    }
}


