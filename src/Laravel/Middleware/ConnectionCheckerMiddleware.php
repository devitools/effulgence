<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Middleware;

use Closure;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\Request;
use Effulgence\Laravel\Database\Relational\LaravelConnectionChecker;

use function Constructo\Cast\integerify;

class ConnectionCheckerMiddleware
{
    public function __construct(
        private readonly LaravelConnectionChecker $connectionChecker,
        private readonly ConfigRepository $config,
    ) {
    }

    public function handle(Request $request, Closure $next): mixed
    {
        $this->connectionChecker->check(
            integerify($this->config->get('effulgence.databases.default.check.max_attempts', 3)),
            integerify($this->config->get('effulgence.databases.default.check.delay_microseconds', 100)),
        );

        return $next($request);
    }
}
