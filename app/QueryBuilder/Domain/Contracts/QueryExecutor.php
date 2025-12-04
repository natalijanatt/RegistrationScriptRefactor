<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Contracts;

interface QueryExecutor
{
    /**
     * @return array<array<string, mixed>>
     */
    public function fetchAll(string $sql, array $params = []): array;
    /**
     * @return array<array<string, mixed>>|null
     */
    public function fetchOne(string $sql, array $params = []): ?array;
    public function execute(string $sql, array $params = []): int;
    public function lastInsertId(?string $name = null): string|false;
}