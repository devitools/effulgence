<?php

declare(strict_types=1);

namespace Effulgence\Test\Presentation\Output;

use Constructo\Contract\Exportable;
use Constructo\Contract\Message;
use Constructo\Support\Set;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Effulgence\Presentation\Output\AlreadyReported;
use Effulgence\Presentation\Output\ImUsed;
use Effulgence\Presentation\Output\MultiStatus;
use Effulgence\Presentation\Output\NonAuthoritative;
use Effulgence\Presentation\Output\Ok;
use Effulgence\Presentation\Output\PartialContent;
use Effulgence\Presentation\Output\ResetContent;

final class SuccessTest extends TestCase
{
    #[DataProvider('successClassesProvider')]
    public function testSuccessClassesWithPrimitiveContent(string $className): void
    {
        $content = 'some content';
        $properties = ['key' => 'value'];

        $instance = $className::createFrom($content, $properties);

        $this->assertEquals($content, $instance->content());
        $this->assertEquals(
            $properties,
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('successClassesProvider')]
    public function testSuccessClassesWithMessage(string $className): void
    {
        $message = $this->createMock(Message::class);
        $message->method('content')
            ->willReturn('message content');
        $message->method('properties')
            ->willReturn(Set::createFrom(['original' => 'value']));

        $additionalProps = ['extra' => 'value'];
        $instance = $className::createFrom($message, $additionalProps);

        $this->assertEquals('message content', $instance->content());
        $this->assertEquals(
            [
                'original' => 'value',
                'extra' => 'value',
            ],
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('successClassesProvider')]
    public function testSuccessClassesWithExportable(string $className): void
    {
        $exportable = $this->createMock(Exportable::class);
        $exportableData = [
            'id' => 123,
            'name' => 'Test',
        ];
        $exportable->method('export')
            ->willReturn($exportableData);

        $instance = $className::createFrom($exportable);

        $this->assertEquals(
            $exportableData,
            $instance->content()
                ->export()
        );
        $this->assertEquals(
            [],
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('successClassesProvider')]
    public function testSuccessClassesWithNull(string $className): void
    {
        $instance = $className::createFrom();

        $this->assertNull($instance->content());
        $this->assertEquals(
            [],
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('successClassesProvider')]
    public function testSuccessClassesWithArrayContent(string $className): void
    {
        $content = [
            'data' => 'word',
            'nested' => ['value' => true],
        ];

        $instance = $className::createFrom($content);

        $this->assertEquals($content, $instance->content());
        $this->assertEquals(
            [],
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('successClassesProvider')]
    public function testSuccessClassesWithBooleanContent(string $className): void
    {
        $content = true;

        $instance = $className::createFrom($content);

        $this->assertEquals($content, $instance->content());
        $this->assertEquals(
            [],
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('successClassesProvider')]
    public function testSuccessClassesWithNumericContent(string $className): void
    {
        $content = 42;

        $instance = $className::createFrom($content);

        $this->assertEquals($content, $instance->content());
        $this->assertEquals(
            [],
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    /**
     * @return array<string, array{class-string}>
     */
    public static function successClassesProvider(): array
    {
        return [
            'Ok' => [Ok::class],
            'AlreadyReported' => [AlreadyReported::class],
            'ImUsed' => [ImUsed::class],
            'MultiStatus' => [MultiStatus::class],
            'NonAuthoritative' => [NonAuthoritative::class],
            'PartialContent' => [PartialContent::class],
            'ResetContent' => [ResetContent::class],
        ];
    }
}
