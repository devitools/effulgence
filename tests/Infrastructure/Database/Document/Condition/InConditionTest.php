<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Database\Document\Condition;

use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Database\Document\Mongo\Condition\InCondition;

class InConditionTest extends TestCase
{
    public function testShouldCompose(): void
    {
        $condition = new InCondition();
        $composed = $condition->compose('A,B');
        $this->assertEquals(
            [
                '$in' => [
                    'A',
                    'B',
                ],
            ],
            $composed
        );
    }
}
