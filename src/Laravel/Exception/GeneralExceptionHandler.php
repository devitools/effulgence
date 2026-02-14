<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Effulgence\Domain\Exception\ThrowableType;
use Effulgence\Infrastructure\Http\ExceptionResponseNormalizer;
use Effulgence\Infrastructure\Http\JsonFormatter;
use Effulgence\Infrastructure\Http\RequestAdditionalFactory;
use Throwable;

use function sprintf;

class GeneralExceptionHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly JsonFormatter $formatter,
        private readonly RequestAdditionalFactory $factory,
        private readonly ExceptionResponseNormalizer $normalizer,
    ) {
    }

    public function handle(Throwable $throwable, Request $request): JsonResponse
    {
        $additional = $this->factory->make($request, $throwable);

        $type = $additional->thrown->type;
        $message = sprintf('<general> %s', $additional->message);
        $context = $additional->context();
        match ($type) {
            ThrowableType::INVALID_INPUT => $this->logger->debug($message, $context),
            ThrowableType::FALLBACK_REQUIRED => $this->logger->info($message, $context),
            ThrowableType::RETRY_AVAILABLE => $this->logger->warning($message, $context),
            ThrowableType::UNRECOVERABLE => $this->logger->error($message, $context),
            default => $this->logger->alert($message, $context),
        };

        $code = $this->normalizer->normalizeStatusCode($throwable);
        $responseType = $this->normalizer->detectType($type);
        $value = $this->normalizer->normalizeBody($responseType, $additional->thrown->resume());
        $contents = $this->formatter->format($value, $responseType);
        return new JsonResponse(json_decode($contents, true), $code);
    }
}
