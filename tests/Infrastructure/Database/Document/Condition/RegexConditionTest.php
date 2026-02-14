<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Database\Document\Condition;

use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Database\Document\Mongo\Condition\RegexCondition;

class RegexConditionTest extends TestCase
{
    public function testShouldComposeOneItem(): void
    {
        $condition = new RegexCondition();
        $composed = $condition->compose('A');
        $this->assertEquals(['$regex' => 'A'], $composed);
    }

    public function testShouldComposeMultipleItem(): void
    {
        $condition = new RegexCondition();
        $composed = $condition->compose('A,B,C');
        $this->assertEquals([
            '$or' => [
                ['$regex' => 'A'],
                ['$regex' => 'B'],
                ['$regex' => 'C'],
            ],
        ], $composed);
    }
}
