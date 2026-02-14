<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Database\Document;

use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Database\Document\Mongo\Search;

class SearchTest extends TestCase
{
    public function testShouldMakeSearchParam(): void
    {
        $search = Search::create();
        $this->assertEquals('status="open"', $search->make('status', 'open'));
        $this->assertEquals(['status' => 'open'], $search->unmake('status=open'));
        $this->assertEquals(['status' => null], $search->unmake('status'));
    }

    public function testShouldParseSimpleExpression(): void
    {
        $search = Search::create();

        $expression = 'status=open';
        $filters = $search->parse($expression);

        $this->assertEquals(
            [
                'status' => 'open',
            ],
            $filters
        );
    }

    public function testShouldParseComplexExpression(): void
    {
        $search = Search::create();

        $expression = 'type=best and ((status=open or (priority=high and date=2024-11-22)))';
        $filters = $search->parse($expression);

        $this->assertEquals(
            [
                '$and' => [
                    [
                        'type' => 'best',
                    ],
                    [
                        '$and' => [
                            [
                                '$or' => [
                                    [
                                        'status' => 'open',
                                    ],
                                ],
                            ],
                            [
                                '$and' => [
                                    [
                                        'priority' => 'high',
                                    ],
                                    [
                                        'date' => '2024-11-22',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $filters
        );
    }
}
