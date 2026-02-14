<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Logging;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Logging\StdoutLogger;
use Effulgence\Laravel\Logging\StdoutLoggerFactory;

final class StdoutLoggerFactoryTest extends TestCase
{
    public function testShouldCreateLogger(): void
    {
        $config = $this->createMock(ConfigRepository::class);
        $config->method('get')
            ->willReturnCallback(function (string $key, mixed $default = null) {
                return $default;
            });

        $factory = new StdoutLoggerFactory($config);
        $logger = $factory(['env' => 'test']);

        $this->assertInstanceOf(StdoutLogger::class, $logger);
    }

    public function testShouldCreateLoggerWithMake(): void
    {
        $config = $this->createMock(ConfigRepository::class);
        $config->method('get')
            ->willReturnCallback(function (string $key, mixed $default = null) {
                return $default;
            });

        $factory = new StdoutLoggerFactory($config);
        $logger = $factory->make('test');

        $this->assertInstanceOf(StdoutLogger::class, $logger);
    }
}
