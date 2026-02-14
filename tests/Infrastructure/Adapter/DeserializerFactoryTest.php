<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Adapter;

use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Adapter\DeserializerFactory;
use Effulgence\Test\Testing\Stub\Stub;

final class DeserializerFactoryTest extends TestCase
{
    public function testShouldCreateDeserializer(): void
    {
        $factory = new DeserializerFactory();
        $deserializer = $factory->make(Stub::class);

        $this->assertEquals(Stub::class, $deserializer->type);
        $this->assertEquals([], $deserializer->formatters);
    }
}
