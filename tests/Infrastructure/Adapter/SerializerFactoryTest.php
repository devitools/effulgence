<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Adapter;

use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Adapter\SerializerFactory;
use Effulgence\Test\Testing\Stub\Stub;

final class SerializerFactoryTest extends TestCase
{
    public function testShouldCreateSerializer(): void
    {
        $factory = new SerializerFactory();
        $serializer = $factory->make(Stub::class);

        $this->assertEquals(Stub::class, $serializer->type);
        $this->assertEquals([], $serializer->formatters);
    }
}
