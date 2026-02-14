<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Http;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Effulgence\Domain\Exception\InvalidInputException;
use Effulgence\Domain\Exception\ThrowableType;
use Effulgence\Infrastructure\Http\ExceptionResponseNormalizer;
use Effulgence\Infrastructure\Http\ResponseType;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;

final class ExceptionResponseNormalizerTest extends TestCase
{
    private ExceptionResponseNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new ExceptionResponseNormalizer();
    }

    public function testShouldNormalizeStatusCodeForValidationException(): void
    {
        $translator = new Translator(new ArrayLoader(), 'en');
        $validator = new Validator($translator, [], []);
        $exception = new ValidationException($validator);
        $exception->status = 422;

        $statusCode = $this->normalizer->normalizeStatusCode($exception);

        $this->assertEquals(422, $statusCode);
    }

    public function testShouldNormalizeStatusCodeForInvalidInputException(): void
    {
        $exception = new InvalidInputException(['Invalid input']);

        $statusCode = $this->normalizer->normalizeStatusCode($exception);

        $this->assertEquals(428, $statusCode);
    }

    public function testShouldNormalizeStatusCodeForGenericException(): void
    {
        $exception = new Exception('Generic error', 404);

        $statusCode = $this->normalizer->normalizeStatusCode($exception);

        $this->assertEquals(404, $statusCode);
    }

    public function testShouldUseDefaultStatusCodeWhenExceptionCodeIsInvalid(): void
    {
        $exception = new Exception('Generic error', 200);

        $statusCode = $this->normalizer->normalizeStatusCode($exception);

        $this->assertEquals(500, $statusCode);
    }

    public function testShouldUseCustomFallbackStatusCode(): void
    {
        $exception = new Exception('Generic error', 200);

        $statusCode = $this->normalizer->normalizeStatusCode($exception, 503);

        $this->assertEquals(503, $statusCode);
    }

    public function testShouldDetectFailResponseTypeForInvalidInput(): void
    {
        $responseType = $this->normalizer->detectType(ThrowableType::INVALID_INPUT);

        $this->assertEquals(ResponseType::FAIL, $responseType);
    }

    public function testShouldDetectFailResponseTypeForFallbackRequired(): void
    {
        $responseType = $this->normalizer->detectType(ThrowableType::FALLBACK_REQUIRED);

        $this->assertEquals(ResponseType::FAIL, $responseType);
    }

    public function testShouldDetectFailResponseTypeForRetryAvailable(): void
    {
        $responseType = $this->normalizer->detectType(ThrowableType::RETRY_AVAILABLE);

        $this->assertEquals(ResponseType::FAIL, $responseType);
    }

    public function testShouldDetectErrorResponseTypeForUnrecoverable(): void
    {
        $responseType = $this->normalizer->detectType(ThrowableType::UNRECOVERABLE);

        $this->assertEquals(ResponseType::ERROR, $responseType);
    }

    public function testShouldDetectErrorResponseTypeForUntreated(): void
    {
        $responseType = $this->normalizer->detectType(ThrowableType::UNTREATED);

        $this->assertEquals(ResponseType::ERROR, $responseType);
    }

    public function testShouldNormalizeBodyForFailWithJsonMessage(): void
    {
        $message = '{"error":"Invalid input","field":"email"}';

        $body = $this->normalizer->normalizeBody(ResponseType::FAIL, $message);

        $this->assertEquals(
            [
                'error' => 'Invalid input',
                'field' => 'email',
            ],
            $body
        );
    }

    public function testShouldNormalizeBodyForFailWithNonJsonMessage(): void
    {
        $message = 'Invalid input';

        $body = $this->normalizer->normalizeBody(ResponseType::FAIL, $message);

        $this->assertEquals(['message' => 'Invalid input'], $body);
    }

    public function testShouldNormalizeBodyForError(): void
    {
        $message = 'Server error';

        $body = $this->normalizer->normalizeBody(ResponseType::ERROR, $message);

        $this->assertEquals('Server error', $body);
    }

    public function testShouldNormalizeBodyForSuccess(): void
    {
        $message = 'Success message';

        $body = $this->normalizer->normalizeBody(ResponseType::SUCCESS, $message);

        $this->assertNull($body);
    }
}
