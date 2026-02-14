<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Testing\Observability\Logger\InMemory;

readonly class Record
{
    public function __construct(
        public string $level,
        public string $message,
        public array $context
    ) {
    }
}
