<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Exception;

use Exception;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Effulgence\Domain\Exception\Parser\Additional;
use Effulgence\Domain\Exception\Parser\Thrown;
use Effulgence\Domain\Exception\ThrowableType;
use Effulgence\Infrastructure\Http\ExceptionResponseNormalizer;
use Effulgence\Infrastructure\Http\JsonFormatter;
use Effulgence\Infrastructure\Http\RequestAdditionalFactory;
use Effulgence\Infrastructure\Http\ResponseType;
use Effulgence\Laravel\Exception\GeneralExceptionHandler;

final class GeneralExceptionHandlerTest extends TestCase
{
    private LoggerInterface $logger;

    private JsonFormatter $formatter;

    private RequestAdditionalFactory $factory;

    private ExceptionResponseNormalizer $normalizer;

    private GeneralExceptionHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->formatter = new JsonFormatter();
        $this->factory = $this->createMock(RequestAdditionalFactory::class);
        $this->normalizer = $this->createMock(ExceptionResponseNormalizer::class);

        $this->handler = new GeneralExceptionHandler(
            $this->logger,
            $this->formatter,
            $this->factory,
            $this->normalizer,
        );
    }

    private function createAdditional(Exception $exception, ThrowableType $type): Additional
    {
        $thrown = Thrown::createFrom($exception, $type);
        return new Additional(
            line: 'POST /api/test',
            body: [],
            headers: [],
            query: [],
            message: $thrown->resume(),
            thrown: $thrown,
            errors: [],
        );
    }

    public function testShouldHandleInvalidInput(): void
    {
        $exception = new Exception('Invalid input');
        $request = Request::create('/api/test', 'POST');
        $additional = $this->createAdditional($exception, ThrowableType::INVALID_INPUT);

        $this->factory->expects($this->once())->method('make')->willReturn($additional);
        $this->logger->expects($this->once())->method('debug');
        $this->normalizer->method('normalizeStatusCode')->willReturn(428);
        $this->normalizer->method('detectType')->willReturn(ResponseType::FAIL);
        $this->normalizer->method('normalizeBody')->willReturn(['message' => 'Invalid input']);

        $response = $this->handler->handle($exception, $request);
        $this->assertEquals(428, $response->getStatusCode());
    }

    public function testShouldHandleFallbackRequired(): void
    {
        $exception = new Exception('Fallback required');
        $request = Request::create('/api/test', 'POST');
        $additional = $this->createAdditional($exception, ThrowableType::FALLBACK_REQUIRED);

        $this->factory->expects($this->once())->method('make')->willReturn($additional);
        $this->logger->expects($this->once())->method('info');
        $this->normalizer->method('normalizeStatusCode')->willReturn(500);
        $this->normalizer->method('detectType')->willReturn(ResponseType::FAIL);
        $this->normalizer->method('normalizeBody')->willReturn('Fallback required');

        $response = $this->handler->handle($exception, $request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testShouldHandleRetryAvailable(): void
    {
        $exception = new Exception('Retry available');
        $request = Request::create('/api/test', 'POST');
        $additional = $this->createAdditional($exception, ThrowableType::RETRY_AVAILABLE);

        $this->factory->expects($this->once())->method('make')->willReturn($additional);
        $this->logger->expects($this->once())->method('warning');
        $this->normalizer->method('normalizeStatusCode')->willReturn(503);
        $this->normalizer->method('detectType')->willReturn(ResponseType::FAIL);
        $this->normalizer->method('normalizeBody')->willReturn('Retry available');

        $response = $this->handler->handle($exception, $request);
        $this->assertEquals(503, $response->getStatusCode());
    }

    public function testShouldHandleUnrecoverable(): void
    {
        $exception = new Exception('Unrecoverable');
        $request = Request::create('/api/test', 'POST');
        $additional = $this->createAdditional($exception, ThrowableType::UNRECOVERABLE);

        $this->factory->expects($this->once())->method('make')->willReturn($additional);
        $this->logger->expects($this->once())->method('error');
        $this->normalizer->method('normalizeStatusCode')->willReturn(500);
        $this->normalizer->method('detectType')->willReturn(ResponseType::ERROR);
        $this->normalizer->method('normalizeBody')->willReturn('Unrecoverable');

        $response = $this->handler->handle($exception, $request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testShouldHandleUntreated(): void
    {
        $exception = new Exception('Untreated');
        $request = Request::create('/api/test', 'POST');
        $additional = $this->createAdditional($exception, ThrowableType::UNTREATED);

        $this->factory->expects($this->once())->method('make')->willReturn($additional);
        $this->logger->expects($this->once())->method('alert');
        $this->normalizer->method('normalizeStatusCode')->willReturn(500);
        $this->normalizer->method('detectType')->willReturn(ResponseType::ERROR);
        $this->normalizer->method('normalizeBody')->willReturn('Untreated');

        $response = $this->handler->handle($exception, $request);
        $this->assertEquals(500, $response->getStatusCode());
    }
}
