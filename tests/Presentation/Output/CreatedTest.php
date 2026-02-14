<?php

declare(strict_types=1);

namespace Effulgence\Test\Presentation\Output;

use PHPUnit\Framework\TestCase;
use Effulgence\Presentation\Output\Created;

final class CreatedTest extends TestCase
{
    public function testShouldHaveIdOnContent(): void
    {
        $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
        $output = Created::createFrom($id);
        $this->assertEquals($id, $output->content());
    }
}
