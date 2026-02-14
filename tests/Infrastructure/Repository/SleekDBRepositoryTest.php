<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Database\Document\SleekDBFactory;
use Effulgence\Infrastructure\Database\Managed;

final class SleekDBRepositoryTest extends TestCase
{
    public function testResource(): void
    {
        $generator = $this->createMock(Managed::class);
        $databaseFactory = $this->createMock(SleekDBFactory::class);
        $databaseFactory->expects($this->once())
            ->method('make')
            ->with('x');
        new SleekDBRepositoryTestMock($generator, $databaseFactory);
    }
}
