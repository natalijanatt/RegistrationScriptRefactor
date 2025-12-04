<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Contracts;

interface InsertableInterface extends BaseQueryInterface
{
    /**
     * @param array<string, mixed|SqlExpression> $values
     */
    public function insert(array $values): int;
}
