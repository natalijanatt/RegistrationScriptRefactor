<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Contracts;

interface SelectableInterface extends BaseQueryInterface
{
    /**
     * @param array<int, string|SqlExpression> $columns
     */
    public function select(array $columns): static;

    public function where(string $column, string $operator, mixed $value): static;

    public function andWhere(string $column, string $operator, mixed $value): static;

    public function orWhere(string $column, string $operator, mixed $value): static;

    public function orderBy(string $column, string $direction = 'ASC'): static;

    public function limit(int $limit): static;

    public function offset(int $offset): static;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function get(): array;

    /**
     * @return array<string, mixed>|null
     */
    public function first(): ?array;
}
