<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Database\Document\Mongo;

interface Condition
{
    public function compose(string $value): array;
}
