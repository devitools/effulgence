<?php

declare(strict_types=1);

namespace Effulgence\Domain\Contract\Adapter;

interface SerializerFactory
{
    /**
     * @template T of object
     * @param class-string<T> $type
     * @return Serializer<T>
     */
    public function make(string $type): Serializer;
}
