<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Effulgence\Domain\Exception\ManagedException;
use Effulgence\Infrastructure\Adapter\Deserializer;
use Effulgence\Infrastructure\Database\Managed;
use Effulgence\Infrastructure\Database\Relational\Connection;
use Effulgence\Infrastructure\Database\Relational\ConnectionFactory;
use Effulgence\Infrastructure\Repository\Adapter\RelationalDeserializerFactory;
use Effulgence\Infrastructure\Repository\Adapter\RelationalSerializerFactory;
use stdClass;

final class PostgresRepositoryTest extends TestCase
{
    private PostgresRepositoryTestMock $repository;

    private Deserializer $deserializer;

    private RelationalDeserializerFactory $deserializerFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $generator = $this->createMock(Managed::class);
        $this->deserializer = $this->createMock(Deserializer::class);

        $this->deserializerFactory = $this->createMock(RelationalDeserializerFactory::class);
        $serializerFactory = $this->createMock(RelationalSerializerFactory::class);

        $connectionFactory = $this->createMock(ConnectionFactory::class);
        $connectionFactory->expects($this->once())
            ->method('make')
            ->willReturn($this->createMock(Connection::class));

        $this->repository = new PostgresRepositoryTestMock(
            $generator,
            $this->deserializerFactory,
            $serializerFactory,
            $connectionFactory,
        );
    }

    public function testShouldGenerateBindings(): void
    {
        $this->deserializerFactory->expects($this->once())
            ->method('make')
            ->with(stdClass::class)
            ->willReturn($this->deserializer);

        $this->deserializer->expects($this->once())
            ->method('deserialize')
            ->willReturn(['field' => 'value']);

        $values = $this->repository->exposeBindings(
            instance: new stdClass(),
            fields: ['field'],
            generate: [
                'cuid' => 'id',
                'at' => 'timestamp',
            ]
        );
        $this->assertEquals(['value'], $values);
    }

    public function testShouldRaiseMappingExceptionOnInvalidGenerate(): void
    {
        $this->expectException(ManagedException::class);

        $this->repository->exposeBindings(
            instance: new stdClass(),
            fields: ['field'],
            generate: ['field' => 'invalid']
        );
    }

    public function testShouldRenderColumns(): void
    {
        $this->assertEquals(
            '"field_one", "field_two"',
            $this->repository->exposeColumns(
                [
                    'field_one',
                    'field_two',
                ]
            )
        );
    }

    public function testShouldRenderValues(): void
    {
        $this->assertEquals(
            '?, ?',
            $this->repository->exposeWildcards(
                [
                    'field_one',
                    'field_two',
                ]
            )
        );
    }
}
