<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Database\Relational;

interface ConnectionChecker
{
    public function check(int $maxAttempts = 5, int $microseconds = 1000): int;

    public function isAvailable(): bool;
}
