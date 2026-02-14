<?php

declare(strict_types=1);

namespace Effulgence\Presentation\Output;

use Effulgence\Presentation\Output;

final class Created extends Output
{
    public function __construct(string $id)
    {
        parent::__construct(content: $id, properties: ['id' => $id]);
    }

    public static function createFrom(string $id): Created
    {
        return new self($id);
    }
}
