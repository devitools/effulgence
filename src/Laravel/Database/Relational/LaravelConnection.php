<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Database\Relational;

use Closure;
use Constructo\Support\Set;
use Illuminate\Database\Connection as IlluminateConnection;
use Effulgence\Infrastructure\Database\Relational\Connection;

use function Constructo\Cast\arrayify;
use function Constructo\Cast\integerify;
use function get_object_vars;
use function is_object;

class LaravelConnection implements Connection
{
    public function __construct(
        public readonly string $name,
        private readonly IlluminateConnection $connection,
    ) {
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    public function insert(string $query, array $bindings = []): int
    {
        $this->connection->insert($query, $bindings);
        return integerify($this->connection->getPdo()->lastInsertId());
    }

    public function execute(string $query, array $bindings = []): int
    {
        return $this->connection->affectingStatement($query, $bindings);
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function query(string $query, array $bindings = []): array
    {
        $results = $this->connection->select($query, $bindings);
        return array_map(function (mixed $row) {
            if (is_object($row)) {
                return get_object_vars($row);
            }
            return arrayify($row);
        }, $results);
    }

    public function fetch(string $query, array $bindings = []): Set
    {
        $data = $this->connection->selectOne($query, $bindings);
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        return Set::createFrom(arrayify($data));
    }

    public function run(Closure $closure): void
    {
        $pdo = $this->connection->getPdo();
        $closure($pdo);
    }
}
