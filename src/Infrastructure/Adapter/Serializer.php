<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Adapter;

use Constructo\Contract\Formatter;
use Constructo\Support\Reflective\Notation;
use Constructo\Support\Set;
use Effulgence\Domain\Contract\Adapter\Serializer as Contract;
use Effulgence\Infrastructure\Adapter\Serialize\Builder;

/**
 * @template T of object
 * @implements Contract<T>
 */
class Serializer extends Builder implements Contract
{
    /**
     * @param class-string<T> $type
     * @param array<callable|Formatter> $formatters
     */
    public function __construct(
        public readonly string $type,
        Notation $case = Notation::SNAKE,
        array $formatters = [],
    ) {
        parent::__construct($case, $formatters);
    }

    /**
     * @return T
     */
    public function serialize(array $datum): mixed
    {
        return $this->build($this->type, Set::createFrom($datum));
    }
}
