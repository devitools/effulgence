<?php

declare(strict_types=1);

namespace Effulgence\Test\Presentation\Output;

use PHPUnit\Framework\TestCase;
use Effulgence\Presentation\Output\Fail\NotFound;

final class NotFoundTest extends TestCase
{
    public function testShouldHaveMissingOnContent(): void
    {
        $missing = 'User';
        $what = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
        $properties = ['Missing' => sprintf('"%s" identified by "%s" not found', $missing, $what)];
        $output = NotFound::createFrom($missing, $what);
        $this->assertNull($output->content());
        $this->assertEquals(
            $properties,
            $output->properties()
                ->toArray()
        );
    }
}
