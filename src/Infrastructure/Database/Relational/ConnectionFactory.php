<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Database\Relational;

interface ConnectionFactory
{
    public function make(string $connection): Connection;
}
