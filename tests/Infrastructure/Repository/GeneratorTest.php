<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Effulgence\Domain\Exception\ManagedException;
use Effulgence\Infrastructure\Database\Managed;

final class GeneratorTest extends TestCase
{
    final public function testId(): void
    {
        $generator = new Managed();
        $id = $generator->id();
        $this->assertIsString($id);
        $this->assertGreaterThanOrEqual(4, strlen($id));
        $this->assertLessThanOrEqual(32, strlen($id));
    }

    final public function testNow(): void
    {
        $generator = new Managed();
        $now = $generator->now();
        $this->assertIsString($now);
    }

    final public function testIdWithLength(): void
    {
        $this->expectException(ManagedException::class);
        $this->expectExceptionMessage('Error generating "id": "maxLength: cannot be less than 4 or greater than 32."');
        $generator = new Managed(0);
        $generator->id();
    }
}
