<?php

declare(strict_types=1);

namespace Effulgence\Test\Presentation\Output;

use PHPUnit\Framework\TestCase;
use Effulgence\Presentation\Output\NoContent;

final class NoContentTest extends TestCase
{
    public function testShouldHaveNoContent(): void
    {
        $properties = ['word' => 'test'];
        $output = NoContent::createFrom($properties);
        $this->assertNull($output->content());
        $this->assertEquals(
            $properties,
            $output->properties()
                ->toArray()
        );
    }
}
