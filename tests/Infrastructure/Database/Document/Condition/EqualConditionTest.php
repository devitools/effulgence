<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Database\Document\Condition;

use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Database\Document\Mongo\Condition\EqualCondition;

class EqualConditionTest extends TestCase
{
    public function testShouldCompose(): void
    {
        $condition = new EqualCondition();
        $composed = $condition->compose('open');
        $this->assertEquals(['$eq' => 'open'], $composed);
    }

    #[RequiresPhpExtension('mongodb')]
    public function testShouldComposeDate(): void
    {
        $condition = new EqualCondition();
        $composed = $condition->compose('2024-11-17');
        $this->assertArrayHasKey('$eq', $composed);
        $this->assertInstanceOf(UTCDateTime::class, $composed['$eq']);
        $this->assertEquals(
            1731801600,
            $composed['$eq']->toDateTime()
                ->getTimestamp()
        );
    }
}
