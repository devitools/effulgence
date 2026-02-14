<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Support;

use Exception;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use PHPUnit\Framework\TestCase;
use Effulgence\Domain\Exception\ThrowableType;
use Effulgence\Laravel\Support\LaravelThrownFactory;

final class LaravelThrownFactoryTest extends TestCase
{
    public function testShouldMakeThrown(): void
    {
        $classification = [Exception::class => ThrowableType::UNRECOVERABLE];
        $config = $this->createMock(ConfigRepository::class);
        $config->expects($this->once())
            ->method('get')
            ->with('effulgence.exceptions.classification', [])
            ->willReturn($classification);

        $factory = new LaravelThrownFactory($config);
        $thrown = $factory->make(new Exception('test'));

        $this->assertEquals(ThrowableType::UNRECOVERABLE, $thrown->type);
    }
}
