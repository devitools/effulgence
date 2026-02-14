<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Exception;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Effulgence\Domain\Exception\InvalidInputException;
use Effulgence\Domain\Exception\Parser\Additional;
use Effulgence\Domain\Exception\Parser\Thrown;
use Effulgence\Domain\Exception\ThrowableType;
use Effulgence\Infrastructure\Http\ExceptionResponseNormalizer;
use Effulgence\Infrastructure\Http\JsonFormatter;
use Effulgence\Infrastructure\Http\RequestAdditionalFactory;
use Effulgence\Laravel\Exception\ValidationExceptionHandler;

final class ValidationExceptionHandlerTest extends TestCase
{
    private LoggerInterface $logger;

    private JsonFormatter $formatter;

    private RequestAdditionalFactory $factory;

    private ExceptionResponseNormalizer $normalizer;

    private ValidationExceptionHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->formatter = new JsonFormatter();
        $this->factory = $this->createMock(RequestAdditionalFactory::class);
        $this->normalizer = $this->createMock(ExceptionResponseNormalizer::class);

        $this->handler = new ValidationExceptionHandler(
            $this->logger,
            $this->formatter,
            $this->factory,
            $this->normalizer,
        );
    }

    public function testShouldHandleValidationException(): void
    {
        $translator = new Translator(new ArrayLoader(), 'en');
        $validator = new Validator($translator, [], []);
        $exception = new ValidationException($validator);
        $request = Request::create('/api/test', 'POST');

        $thrown = Thrown::createFrom($exception, ThrowableType::INVALID_INPUT);
        $additional = new Additional(
            line: 'POST /api/test',
            body: [],
            headers: [],
            query: [],
            message: $thrown->resume(),
            thrown: $thrown,
            errors: [],
        );

        $this->factory->expects($this->once())->method('make')->willReturn($additional);
        $this->logger->expects($this->once())->method('debug');
        $this->normalizer->expects($this->once())->method('normalizeStatusCode')->willReturn(422);

        $response = $this->handler->handle($exception, $request);

        $this->assertNotNull($response);
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testShouldHandleInvalidInputException(): void
    {
        $exception = new InvalidInputException(['field' => 'error']);
        $request = Request::create('/api/test', 'POST');

        $thrown = Thrown::createFrom($exception, ThrowableType::INVALID_INPUT);
        $additional = new Additional(
            line: 'POST /api/test',
            body: [],
            headers: [],
            query: [],
            message: $thrown->resume(),
            thrown: $thrown,
            errors: ['field' => 'error'],
        );

        $this->factory->expects($this->once())->method('make')->willReturn($additional);
        $this->logger->expects($this->once())->method('debug');
        $this->normalizer->expects($this->once())->method('normalizeStatusCode')->willReturn(400);

        $response = $this->handler->handle($exception, $request);

        $this->assertNotNull($response);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturnNullForNonValidationException(): void
    {
        $exception = new Exception('Not a validation exception');
        $request = Request::create('/api/test', 'POST');

        $response = $this->handler->handle($exception, $request);

        $this->assertNull($response);
    }
}
