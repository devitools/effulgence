<?php

declare(strict_types=1);

namespace Effulgence\Presentation\Output;

use Effulgence\Presentation\Output;

final class NoContent extends Output
{
    public function __construct(array $properties = [])
    {
        parent::__construct(null, $properties);
    }

    public static function createFrom(array $properties = []): NoContent
    {
        return new self($properties);
    }
}
