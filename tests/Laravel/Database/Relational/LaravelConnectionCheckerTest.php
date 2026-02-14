<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Database\Relational;

use PHPUnit\Framework\TestCase;
use Effulgence\Laravel\Database\Relational\LaravelConnection;
use Effulgence\Laravel\Database\Relational\LaravelConnectionChecker;

final class LaravelConnectionCheckerTest extends TestCase
{
    public function testShouldCheckConnectionSuccessfully(): void
    {
        $connection = $this->createMock(LaravelConnection::class);
        $connection->expects($this->once())
            ->method('run');

        $checker = new LaravelConnectionChecker($connection);
        $attempts = $checker->check(1, 100);

        $this->assertEquals(1, $attempts);
    }

    public function testShouldRetryOnFailure(): void
    {
        $connection = $this->createMock(LaravelConnection::class);
        $connection->expects($this->exactly(3))
            ->method('run')
            ->willThrowException(new \RuntimeException('Connection failed'));

        $checker = new LaravelConnectionChecker($connection);
        $attempts = $checker->check(3, 1);

        $this->assertEquals(3, $attempts);
    }
}
