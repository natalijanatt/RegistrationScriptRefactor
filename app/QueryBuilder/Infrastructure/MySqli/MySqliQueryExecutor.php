<?php

declare(strict_types=1);

namespace App\QueryBuilder\Infrastructure\MySqli;

use App\QueryBuilder\Domain\Contracts\QueryExecutor;
use App\QueryBuilder\Domain\Exceptions\QueryExecuteException;
use App\QueryBuilder\Domain\Exceptions\QueryPrepareException;
use mysqli;
use mysqli_stmt;

class MySqliQueryExecutor implements QueryExecutor
{
    public function __construct(
        private readonly mysqli $mysqli
    ) {
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt   = $this->prepareAndExecute($sql, $params);
        $result = $stmt->get_result();

        if (!$result) {
            $stmt->close();
            return [];
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $stmt->close();

        return $rows;
    }

    /**
     * @inheritDoc
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt   = $this->prepareAndExecute($sql, $params);
        $result = $stmt->get_result();

        if (!$result) {
            $stmt->close();
            return null;
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Execute a write query (INSERT/UPDATE/DELETE).
     *
     * For INSERT: returns last insert id.
     * For UPDATE/DELETE: returns affected rows.
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->prepareAndExecute($sql, $params);

        $affectedRows = $stmt->affected_rows;

        // mysqli::$insert_id is > 0 only for INSERT (or similar auto-id ops)
        $insertId = $this->mysqli->insert_id;

        $stmt->close();

        if ($insertId > 0) {
            return $insertId;
        }

        return $affectedRows;
    }

    /**
     * Optional helper if you need it independently of execute().
     */
    public function lastInsertId(?string $name = null): string|false
    {
        $id = $this->mysqli->insert_id;

        return $id > 0 ? (string) $id : false;
    }

    /**
     * Prepare a statement, bind params and execute it.
     *
     * @throws QueryPrepareException
     * @throws QueryExecuteException
     */
    private function prepareAndExecute(string $sql, array $params): mysqli_stmt
    {
        $stmt = $this->mysqli->prepare($sql);

        if (!$stmt) {
            throw QueryPrepareException::fromMysqliError($this->mysqli->error, $sql);
        }

        if ($params !== []) {
            $types = $this->inferParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw QueryExecuteException::fromMysqliError($stmt->error, $sql, $params);
        }

        return $stmt;
    }

    private function inferParamTypes(array $params): string
    {
        $types = '';

        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                // fallback: send as string
                $types .= 's';
            }
        }

        return $types;
    }
}
