<?php

declare(strict_types=1);

namespace App\QueryBuilder\Infrastructure\Builder;

use App\QueryBuilder\Domain\Contracts\QueryBuilderInterface;
use App\QueryBuilder\Domain\Contracts\QueryExecutor;
use App\QueryBuilder\Domain\Contracts\SqlDialect;
use App\QueryBuilder\Domain\Contracts\SqlExpression;
use App\QueryBuilder\Domain\Exceptions\QueryBuilderException;

final class QueryBuilder implements QueryBuilderInterface
{
    private ?string $table = null;
    private ?string $alias = null;

    /** @var array<int, string|SqlExpression> */
    private array $columns = ['*'];
    /** @var array<int, array{boolean: string, expression: string}> */
    private array $wheres = [];

    /** @var array<int, mixed> */
    private array $bindings = [];

    /** @var array<int, string> */
    private array $orderBy = [];

    private ?int $limitVal = null;
    private ?int $offsetVal = null;

    public function __construct(
        private readonly SqlDialect    $dialect,
        private readonly QueryExecutor $executor
    )
    {
    }

    public function table(string $table, ?string $alias = null): static
    {
        $this->table = $table;
        $this->alias = $alias;

        return $this;
    }

    public function select(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function where(string $column, string $operator, mixed $value): static
    {
        return $this->addWhere('AND', $column, $operator, $value, true);
    }

    public function andWhere(string $column, string $operator, mixed $value): static
    {
        return $this->addWhere('AND', $column, $operator, $value);
    }

    public function orWhere(string $column, string $operator, mixed $value): static
    {
        return $this->addWhere('OR', $column, $operator, $value);
    }

    private function addWhere(
        string $boolean,
        string $column,
        string $operator,
        mixed  $value,
        bool   $first = false
    ): static
    {
        if ($first || empty($this->wheres)) {
            $boolean = 'AND';
        }

        if ($value instanceof SqlExpression) {
            $expression = sprintf(
                '%s %s %s',
                $column,
                $operator,
                $value->toSql($this->dialect)
            );
        } else {
            $placeholder = $this->dialect->placeholder(count($this->bindings) + 1);

            $expression = sprintf('%s %s %s', $column, $operator, $placeholder);
            $this->bindings[] = $value;
        }

        $this->wheres[] = [
            'boolean' => $boolean,
            'expression' => $expression,
        ];

        return $this;
    }


    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderBy[] = "$column $dir";
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limitVal = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offsetVal = $offset;
        return $this;
    }

    public function get(): array
    {
        return $this->executor->fetchAll(
            $this->toSql(),
            $this->getBindings()
        );
    }

    public function first(): ?array
    {
        return $this->executor->fetchOne(
            $this->toSql(),
            $this->getBindings()
        );
    }

    public function toSql(): string
    {
        if ($this->table === null) {
            throw QueryBuilderException::missingTable();
        }

        $columnsSql = implode(', ', array_map(
            function (string|SqlExpression $column): string {
                if ($column instanceof SqlExpression) {
                    return $column->toSql($this->dialect);
                }

                return $column;
            },
            $this->columns
        ));
        $tableSql = $this->alias
            ? "$this->table AS $this->alias"
            : $this->table;

        $sql = "SELECT $columnsSql FROM $tableSql";

        if (!empty($this->wheres)) {
            $whereParts = [];

            foreach ($this->wheres as $index => $w) {
                $prefix = $index === 0 ? '' : " {$w['boolean']} ";
                $whereParts[] = $prefix . $w['expression'];
            }

            $sql .= " WHERE " . implode('', $whereParts);
        }
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        $sql .= $this->dialect->compileLimitOffset(
            $this->limitVal,
            $this->offsetVal
        );

        return $sql;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function insert(array $values): int
    {
        if ($this->table === null) {
            throw new \LogicException('QueryBuilder: table() must be set before insert().');
        }

        if ($values === []) {
            throw new \InvalidArgumentException('Insert values cannot be empty.');
        }

        $columns = [];
        $placeholders = [];
        $params = [];

        foreach ($values as $column => $value) {
            $columns[] = $column;

            if ($value instanceof SqlExpression) {
                $placeholders[] = $value->toSql($this->dialect);
            } else {
                $placeholder = $this->dialect->placeholder(count($params) + 1);
                $placeholders[] = $placeholder;
                $params[] = $value;
            }
        }

        $columnsSql = implode(', ', $columns);
        $placeholdersSql = implode(', ', $placeholders);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            $columnsSql,
            $placeholdersSql
        );

        return $this->executor->execute($sql, $params);
    }
}