<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Http;

use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Http\JsonFormatter;
use Effulgence\Infrastructure\Http\ResponseType;
use stdClass;

final class JsonFormatterTest extends TestCase
{
    public function testShouldFormatSuccess(): void
    {
        $formatter = new JsonFormatter();
        $formatted = $formatter->format(new stdClass());
        $this->assertEquals('{"status":"success","data":{}}', $formatted);
    }

    public function testShouldFormatFail(): void
    {
        $formatter = new JsonFormatter();
        $formatted = $formatter->format([], ResponseType::FAIL);
        $this->assertEquals('{"status":"fail","data":[]}', $formatted);
    }

    public function testShouldFormatError(): void
    {
        $formatter = new JsonFormatter();
        $formatted = $formatter->format(null, ResponseType::ERROR);
        $this->assertEquals('{"status":"error","message":null}', $formatted);
    }

    public function testShouldThrowsExceptionWhenTypeIsInvalid(): void
    {
        $formatter = new JsonFormatter();
        $formatted = $formatter->format(null, 200);
        $message = addslashes(sprintf("The 'option' must be an instance of '%s'.", ResponseType::class));
        $this->assertEquals(sprintf('{"status":"error","message":"%s"}', $message), $formatted);
    }
}
