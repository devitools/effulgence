<?php

declare(strict_types=1);

namespace Effulgence\Presentation\Output\Fail;

use Effulgence\Presentation\Output;

use function sprintf;

final class NotFound extends Output
{
    public function __construct(string $missing, int|string $what)
    {
        $properties = [
            'Missing' => sprintf('"%s" identified by "%s" not found', $missing, $what),
        ];
        parent::__construct(null, $properties);
    }

    public static function createFrom(string $missing, int|string $what): NotFound
    {
        return new self($missing, $what);
    }
}
