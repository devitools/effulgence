<?php

declare(strict_types=1);

namespace Effulgence\Presentation\Output\Error;

use Effulgence\Presentation\Output;

abstract class Error extends Output
{
    final public function __construct(null|int|string $content, array $properties = [])
    {
        parent::__construct($content, $properties);
    }

    final public static function createFrom(null|int|string $content = null, array $properties = []): static
    {
        return new static($content, $properties);
    }
}
