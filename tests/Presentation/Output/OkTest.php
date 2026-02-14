<?php

declare(strict_types=1);

namespace Effulgence\Test\Presentation\Output;

use Constructo\Support\Entity;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Effulgence\Presentation\Output\Ok;
use stdClass;

class OkTest extends TestCase
{
    public function testShouldHaveNoContent(): void
    {
        $properties = ['word' => 'test'];
        $output = Ok::createFrom(properties: $properties);
        $this->assertNull($output->content());
        $this->assertEquals(
            $properties,
            $output->properties()
                ->toArray()
        );
    }

    #[TestWith([1])]
    #[TestWith([1.1])]
    #[TestWith(['word'])]
    #[TestWith([['word' => 'word']])]
    #[TestWith([null])]
    #[TestWith([true])]
    #[TestWith([new stdClass()])]
    public function testShouldHandleMixedContent(mixed $content): void
    {
        $output = Ok::createFrom($content);
        $this->assertEquals($content, $output->content());
        $this->assertEquals(
            [],
            $output->properties()
                ->toArray()
        );
    }

    public function testShouldHandleMessage(): void
    {
        $message = Ok::createFrom('message', ['id' => 1234567890]);
        $output = Ok::createFrom($message);
        $this->assertEquals('message', $output->content());
        $this->assertEquals(
            ['id' => 1234567890],
            $output->properties()
                ->toArray()
        );
    }

    public function testShouldHandleExportable(): void
    {
        $entity = new class extends Entity {
            public string $value = 'none';
        };
        $output = Ok::createFrom($entity);
        $this->assertEquals('none', $output->content()->value);
    }
}
