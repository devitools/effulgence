<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;
use Effulgence\Domain\Exception\InvalidInputException;
use Effulgence\Infrastructure\Http\ExceptionResponseNormalizer;
use Effulgence\Infrastructure\Http\JsonFormatter;
use Effulgence\Infrastructure\Http\RequestAdditionalFactory;
use Effulgence\Infrastructure\Http\ResponseType;
use Throwable;

use function sprintf;

class ValidationExceptionHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly JsonFormatter $formatter,
        private readonly RequestAdditionalFactory $factory,
        private readonly ExceptionResponseNormalizer $normalizer,
    ) {
    }

    public function handle(Throwable $throwable, Request $request): ?JsonResponse
    {
        if (! ($throwable instanceof ValidationException) && ! ($throwable instanceof InvalidInputException)) {
            return null;
        }

        $additional = $this->factory->make($request, $throwable);

        $message = sprintf('<validation> %s', $additional->message);
        $context = $additional->context();
        $this->logger->debug($message, $context);

        $statusCode = $this->normalizer->normalizeStatusCode($throwable, 400);
        $contents = $this->formatter->format($context, ResponseType::FAIL);
        return new JsonResponse(
            json_decode($contents, true),
            $statusCode,
            ['content-type' => 'application/json; charset=utf-8']
        );
    }
}
