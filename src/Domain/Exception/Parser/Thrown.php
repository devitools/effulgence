<?php

declare(strict_types=1);

namespace Effulgence\Domain\Exception\Parser;

use DateTimeImmutable;
use Effulgence\Domain\Exception\ThrowableType;
use Throwable;

class Thrown
{
    public function __construct(
        public readonly ThrowableType $type,
        public readonly DateTimeImmutable $at,
        public readonly string $kind,
        public readonly string $message,
        public readonly int $code,
        public readonly string $file,
        public readonly int $line,
        public readonly array $trace,
        public readonly ?Thrown $previous = null,
    ) {
    }

    public static function createFrom(Throwable $throwable, ThrowableType $type = ThrowableType::UNTREATED): Thrown
    {
        $previous = $throwable->getPrevious();
        return new self(
            type: $type,
            at: new DateTimeImmutable(),
            kind: $throwable::class,
            message: $throwable->getMessage(),
            code: $throwable->getCode(),
            file: $throwable->getFile(),
            line: $throwable->getLine(),
            trace: $throwable->getTrace(),
            previous: $previous
                ? self::createFrom($previous, $type)
                : null,
        );
    }

    public function resume(): string
    {
        return sprintf('"%s" in `%s` at `%s`', $this->message, $this->file, $this->line);
    }

    public function context(): array
    {
        return [
            'type' => $this->type->value,
            'at' => $this->at,
            'kind' => $this->kind,
            'message' => $this->message,
            'code' => $this->code,
            'file' => $this->file,
            'line' => $this->line,
            'trace' => $this->trace,
            'previous' => $this->previous?->context(),
        ];
    }
}
