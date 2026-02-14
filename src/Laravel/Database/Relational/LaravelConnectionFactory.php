<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Database\Relational;

use Illuminate\Database\DatabaseManager;
use Effulgence\Infrastructure\Database\Relational\Connection;
use Effulgence\Infrastructure\Database\Relational\ConnectionFactory;

class LaravelConnectionFactory implements ConnectionFactory
{
    public function __construct(private readonly DatabaseManager $database)
    {
    }

    public function make(string $connection): Connection
    {
        return new LaravelConnection($connection, $this->database->connection($connection));
    }
}
