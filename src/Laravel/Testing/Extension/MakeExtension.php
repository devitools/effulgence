<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Testing\Extension;

/**
 * @phpstan-ignore trait.unused
 */
trait MakeExtension
{
    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     *
     * @return T
     */
    protected function make(string $class, array $args = []): mixed
    {
        return app()->make($class, $args);
    }
}
