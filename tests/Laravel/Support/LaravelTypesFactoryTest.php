<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Support;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use PHPUnit\Framework\TestCase;
use Effulgence\Laravel\Support\LaravelTypesFactory;

final class LaravelTypesFactoryTest extends TestCase
{
    public function testShouldCreateFactory(): void
    {
        $types = ['custom_type' => 'SomeClass'];
        $config = $this->createMock(ConfigRepository::class);
        $config->expects($this->once())
            ->method('get')
            ->with('effulgence.schema.types', [])
            ->willReturn($types);

        $factory = new LaravelTypesFactory($config);

        $this->assertNotNull($factory);
    }
}
