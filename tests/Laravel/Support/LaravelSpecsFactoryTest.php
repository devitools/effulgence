<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Support;

use Constructo\Core\Serialize\Builder;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use PHPUnit\Framework\TestCase;
use Effulgence\Laravel\Support\LaravelSpecsFactory;

final class LaravelSpecsFactoryTest extends TestCase
{
    public function testShouldCreateFactory(): void
    {
        $specs = ['field' => ['required', 'string']];
        $builder = $this->createMock(Builder::class);
        $config = $this->createMock(ConfigRepository::class);
        $config->expects($this->once())
            ->method('get')
            ->with('effulgence.schema.specs', [])
            ->willReturn($specs);

        $factory = new LaravelSpecsFactory($builder, $config);

        $this->assertNotNull($factory);
    }
}
