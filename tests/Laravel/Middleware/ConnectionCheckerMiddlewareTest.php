<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Middleware;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Effulgence\Laravel\Database\Relational\LaravelConnection;
use Effulgence\Laravel\Database\Relational\LaravelConnectionChecker;
use Effulgence\Laravel\Middleware\ConnectionCheckerMiddleware;

final class ConnectionCheckerMiddlewareTest extends TestCase
{
    public function testShouldCallConnectionChecker(): void
    {
        $config = $this->createMock(ConfigRepository::class);
        $config->method('get')
            ->willReturnMap([
                ['effulgence.databases.default.check.max_attempts', 3, 5],
                ['effulgence.databases.default.check.delay_microseconds', 100, 200],
            ]);

        $laravelConnection = $this->createMock(LaravelConnection::class);
        $laravelConnection->expects($this->once())
            ->method('run');

        $connectionChecker = new LaravelConnectionChecker($laravelConnection);

        $middleware = new ConnectionCheckerMiddleware($connectionChecker, $config);
        $request = Request::create('/api/test', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }
}
