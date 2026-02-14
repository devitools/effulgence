<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Database\Document;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Effulgence\Domain\Exception\Misconfiguration;
use Effulgence\Infrastructure\Database\Document\Mongo\Condition\InCondition;
use Effulgence\Infrastructure\Database\Document\Mongo\ConditionParser;
use stdClass;

class ConditionParserTest extends TestCase
{
    public function testShouldParse(): void
    {
        $parser = new ConditionParser();
        $parsed = $parser->parse('key', 'value');
        $this->assertEquals(['key' => 'value'], $parsed);
    }

    public function testShouldParseUsingCondition(): void
    {
        $parser = new ConditionParser([
            'in' => InCondition::class,
        ]);
        $parser->parse('key', 'in:value');
        $parsed = $parser->parse('key', 'in:value');
        $this->assertEquals(['key' => ['$in' => ['value']]], $parsed);
    }

    public function testShouldParseUsingNotCondition(): void
    {
        $parser = new ConditionParser([
            'in' => InCondition::class,
        ]);
        $parsed = $parser->parse('key', '!in:value');
        $this->assertEquals(['key' => ['$not' => ['$in' => ['value']]]], $parsed);
    }

    public function testShouldReuseCreatedCondition(): void
    {
        $parser = new ConditionParser([
            'in' => InCondition::class,
        ]);
        $parsed = $parser->parse('key', '!in:value');
        $this->assertEquals(['key' => ['$not' => ['$in' => ['value']]]], $parsed);
    }

    public function testShouldFailOnInvalidCondition(): void
    {
        $this->expectException(Misconfiguration::class);
        $parser = new ConditionParser();
        $parser->parse('key', 'none:value');
    }

    public function testShouldFailOnInvalidConfiguration(): void
    {
        $parser = new ConditionParser();
        $reflectionClass = new ReflectionClass($parser);
        $reflectionClass->getProperty('conditions')
            ->setValue($parser, ['none' => stdClass::class]);

        $this->expectException(Misconfiguration::class);
        $parser->parse('key', 'none:value');
    }
}
