<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Middleware;

use Closure;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\Request;
use Effulgence\Domain\Support\Task;

use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;
use function Constructo\Notation\upperify;

class TaskMiddleware
{
    public function __construct(
        private readonly Task $task,
        private readonly ConfigRepository $config,
    ) {
    }

    public function handle(Request $request, Closure $next): mixed
    {
        $operation = sprintf(
            '%s:%s',
            upperify($request->getMethod()),
            $request->getPathInfo()
        );
        $this->task
            ->setResource($operation)
            ->setCorrelationId($this->extractCorrelationId($request))
            ->setInvokerId($this->extractPlatformId($request));

        return $next($request);
    }

    private function extractCorrelationId(Request $request): string
    {
        $location = $this->location($request, 'correlation_id', ['X-Correlation-ID', 'header']);
        return $this->extract($request, ...$location) ?: 'N/A';
    }

    private function extractPlatformId(Request $request): string
    {
        $location = $this->location($request, 'invoker_id', ['X-Invoker-ID', 'header']);
        return $this->extract($request, ...$location) ?: 'N/A';
    }

    /**
     * @return array<string>
     */
    private function location(Request $request, string $key, array $default): array
    {
        $path = sprintf(
            'effulgence.task.%s:%s.%s',
            upperify($request->getMethod()),
            $request->getPathInfo(),
            $key,
        );
        $location = arrayify($this->config->get($path, []));
        if (empty($location)) {
            $path = sprintf('effulgence.task.default.%s', $key);
            $location = arrayify($this->config->get($path, $default));
        }
        return array_map(fn ($item) => stringify($item), $location);
    }

    private function extract(Request $request, string $key, string $type = ''): string
    {
        $extracted = match ($type) {
            'header' => $request->header($key, ''),
            'query' => $request->query($key, ''),
            'cookie' => $request->cookie($key, ''),
            'body' => data_get($request->all(), $key, ''),
            default => '',
        };
        return stringify($extracted);
    }
}
