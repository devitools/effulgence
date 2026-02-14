<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Adapter;

use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Adapter\Serializer;
use Effulgence\Test\Testing\Stub\Stub;

final class SerializerTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = new Serializer(Stub::class);
    }

    public function testSerialize(): void
    {
        $datum = [
            'foo' => 'John Doe',
            'bar' => 30,
        ];

        $result = $this->serializer->serialize($datum);

        $this->assertEquals('John Doe', $result->foo);
        $this->assertEquals(30, $result->bar);
    }
}
