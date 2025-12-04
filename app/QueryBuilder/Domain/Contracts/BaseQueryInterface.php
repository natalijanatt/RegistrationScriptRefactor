<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Contracts;

interface BaseQueryInterface
{
    public function table(string $table, ?string $alias = null): static;

    public function toSql(): string;

    /**
     * @return array<int, mixed>
     */
    public function getBindings(): array;
}