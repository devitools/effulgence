<?php

declare(strict_types=1);

namespace Effulgence\Domain\Exception;

use DomainException;
use Throwable;

class UniqueKeyViolationException extends DomainException
{
    public function __construct(
        public readonly string $key,
        public readonly string $value,
        public readonly ?string $resource = null,
        ?Throwable $previous = null,
        int $code = 0
    ) {
        parent::__construct(sprintf('Unique key violation: "%s:%s"', $key, $value), $code, $previous);
    }
}
