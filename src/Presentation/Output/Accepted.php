<?php

declare(strict_types=1);

namespace Effulgence\Presentation\Output;

use Effulgence\Presentation\Output;

final class Accepted extends Output
{
    public function __construct(int|string $content)
    {
        parent::__construct(content: $content, properties: ['token' => $content]);
    }

    public static function createFrom(string $trackingId): Accepted
    {
        return new self($trackingId);
    }
}
