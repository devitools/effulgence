<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Database\Relational;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use PHPUnit\Framework\TestCase;
use Effulgence\Laravel\Database\Relational\LaravelConnectionFactory;

final class LaravelConnectionFactoryTest extends TestCase
{
    public function testShouldCreateConnection(): void
    {
        $connection = $this->createMock(Connection::class);
        $databaseManager = $this->createMock(DatabaseManager::class);
        $databaseManager->expects($this->once())
            ->method('connection')
            ->with('pgsql')
            ->willReturn($connection);

        $factory = new LaravelConnectionFactory($databaseManager);
        $result = $factory->make('pgsql');

        $this->assertNotNull($result);
    }
}
