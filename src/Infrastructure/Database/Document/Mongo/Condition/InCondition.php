<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Database\Document\Mongo\Condition;

use Effulgence\Infrastructure\Database\Document\Mongo\Condition;

class InCondition implements Condition
{
    public function compose(string $value): array
    {
        $pieces = array_map(trim(...), explode(',', $value));
        return [
            '$in' => $pieces,
        ];
    }
}
