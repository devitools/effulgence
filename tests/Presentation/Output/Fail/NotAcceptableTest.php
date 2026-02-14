<?php

declare(strict_types=1);

namespace Effulgence\Test\Presentation\Output\Fail;

use PHPUnit\Framework\TestCase;
use Effulgence\Presentation\Output\Fail\NotAcceptable;

final class NotAcceptableTest extends TestCase
{
    public function testShouldHaveTokenOnContent(): void
    {
        $token = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
        $output = NotAcceptable::createFrom($token, ['token' => $token]);
        $this->assertEquals($token, $output->content());
        $this->assertEquals(
            ['token' => $token],
            $output->properties()
                ->toArray()
        );
    }
}
