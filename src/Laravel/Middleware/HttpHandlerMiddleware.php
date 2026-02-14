<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Middleware;

use Closure;
use Constructo\Contract\Exportable;
use Constructo\Contract\Message;
use Constructo\Type\Collection;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionException;
use Effulgence\Domain\Collection\Collection as LegacyCollection;
use Effulgence\Infrastructure\Adapter\Deserialize\Demolisher;
use Effulgence\Infrastructure\Http\JsonFormatter;
use Effulgence\Infrastructure\Http\ResponseType;
use Effulgence\Laravel\Event\HttpHandleCompleted;
use Effulgence\Laravel\Event\HttpHandleInterrupted;
use Effulgence\Laravel\Event\HttpHandleStarted;
use Throwable;

use function Constructo\Cast\integerify;
use function is_string;
use function sprintf;

class HttpHandlerMiddleware
{
    public function __construct(
        private readonly ConfigRepository $config,
        private readonly JsonFormatter $formatter,
        private readonly Demolisher $demolisher,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws ReflectionException
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $this->eventDispatcher->dispatch(new HttpHandleStarted($request));
        try {
            $previous = $next($request);
            $response = match (true) {
                $previous instanceof Message => $this->handleMessage($previous),
                $previous instanceof Exportable => $this->handleExportable($previous),
                default => $previous,
            };
            $this->eventDispatcher->dispatch(new HttpHandleCompleted($request, $response));
            return $response;
        } catch (Throwable $throwable) {
            $this->eventDispatcher->dispatch(new HttpHandleInterrupted($request, $throwable));
            throw $throwable;
        }
    }

    /**
     * @throws ReflectionException
     */
    private function handleMessage(Message $message): JsonResponse
    {
        $statusCode = $this->detectStatusCode($message);

        $headers = ['content-type' => 'application/json'];
        $headers = $this->configureHeaders($message, $headers);

        if ($statusCode === 204) {
            return new JsonResponse(null, $statusCode, $headers);
        }

        $value = $this->demolish($message->content());
        $option = $this->detectType($statusCode);
        $contents = $this->formatter->format($value, $option);
        return new JsonResponse(json_decode($contents, true), $statusCode, $headers);
    }

    /**
     * @throws ReflectionException
     */
    private function handleExportable(Exportable $exportable): JsonResponse
    {
        $value = $this->demolish($exportable);
        $contents = $this->formatter->format($value);
        return new JsonResponse(json_decode($contents, true), 200);
    }

    /**
     * @throws ReflectionException
     */
    private function demolish(mixed $value): mixed
    {
        return match (true) {
            $value instanceof LegacyCollection,
            $value instanceof Collection => $this->demolisher->demolishCollection($value),
            is_object($value) => $this->demolisher->demolish($value),
            default => $value,
        };
    }

    private function detectStatusCode(Message $response): int
    {
        $statusCode = integerify($this->config->get(sprintf('effulgence.http.result.%s.status', $response::class)));
        if ($statusCode === 0) {
            return 200;
        }
        return $statusCode;
    }

    private function configureHeaders(Message $message, array $headers): array
    {
        $properties = $message->properties()
            ->toArray();
        foreach ($properties as $key => $value) {
            if (! is_string($value)) {
                continue;
            }
            $headers[sprintf('X-%s', $key)] = $value;
        }
        return $headers;
    }

    private function detectType(int $statusCode): ?ResponseType
    {
        return match (true) {
            $statusCode >= 200 && $statusCode < 300 => ResponseType::SUCCESS,
            $statusCode >= 400 && $statusCode < 500 => ResponseType::FAIL,
            $statusCode >= 500 => ResponseType::ERROR,
            default => null,
        };
    }
}
