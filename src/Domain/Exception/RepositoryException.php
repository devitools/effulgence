<?php

declare(strict_types=1);

namespace Effulgence\Domain\Exception;

use Exception;
use Throwable;

class RepositoryException extends Exception
{
    public readonly ThrowableType $type;

    public function __construct(
        public readonly string $context,
        ?Throwable $previous = null,
        ?ThrowableType $type = null,
        ?string $message = null,
        ?int $code = null,
    ) {
        parent::__construct(
            $message ?? $previous?->getMessage() ?? '',
            $code ?? $previous?->getCode() ?? 0,
            $previous
        );

        $this->type = $type ?? ThrowableType::UNTREATED;
    }
}
