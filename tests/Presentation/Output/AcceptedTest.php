<?php

declare(strict_types=1);

namespace Effulgence\Test\Presentation\Output;

use PHPUnit\Framework\TestCase;
use Effulgence\Presentation\Output\Accepted;

final class AcceptedTest extends TestCase
{
    public function testShouldHaveTokenOnContent(): void
    {
        $token = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
        $output = Accepted::createFrom($token);
        $this->assertEquals($token, $output->content());
        $this->assertEquals(
            ['token' => $token],
            $output->properties()
                ->toArray()
        );
    }
}
