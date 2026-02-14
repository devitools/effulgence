<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Middleware;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Effulgence\Domain\Support\Task;
use Effulgence\Laravel\Middleware\TaskMiddleware;

final class TaskMiddlewareTest extends TestCase
{
    public function testShouldExtractCorrelationIdFromHeader(): void
    {
        $task = new Task();
        $config = $this->createMock(ConfigRepository::class);
        $config->method('get')
            ->willReturnMap([
                ['effulgence.task.GET:/api/test.correlation_id', [], []],
                ['effulgence.task.default.correlation_id', ['X-Correlation-ID', 'header'], ['X-Correlation-ID', 'header']],
                ['effulgence.task.GET:/api/test.invoker_id', [], []],
                ['effulgence.task.default.invoker_id', ['X-Invoker-ID', 'header'], ['X-Invoker-ID', 'header']],
            ]);

        $middleware = new TaskMiddleware($task, $config);
        $request = Request::create('/api/test', 'GET', [], [], [], [
            'HTTP_X_CORRELATION_ID' => 'corr-123',
            'HTTP_X_INVOKER_ID' => 'inv-456',
        ]);

        $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('corr-123', $task->getCorrelationId());
        $this->assertEquals('inv-456', $task->getInvokerId());
    }

    public function testShouldSetNAWhenCorrelationIdNotFound(): void
    {
        $task = new Task();
        $config = $this->createMock(ConfigRepository::class);
        $config->method('get')
            ->willReturnMap([
                ['effulgence.task.GET:/api/test.correlation_id', [], []],
                ['effulgence.task.default.correlation_id', ['X-Correlation-ID', 'header'], ['X-Correlation-ID', 'header']],
                ['effulgence.task.GET:/api/test.invoker_id', [], []],
                ['effulgence.task.default.invoker_id', ['X-Invoker-ID', 'header'], ['X-Invoker-ID', 'header']],
            ]);

        $middleware = new TaskMiddleware($task, $config);
        $request = Request::create('/api/test', 'GET');

        $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('N/A', $task->getCorrelationId());
        $this->assertEquals('N/A', $task->getInvokerId());
    }

    public function testShouldSetResource(): void
    {
        $task = new Task();
        $config = $this->createMock(ConfigRepository::class);
        $config->method('get')
            ->willReturnMap([
                ['effulgence.task.POST:/api/users.correlation_id', [], []],
                ['effulgence.task.default.correlation_id', ['X-Correlation-ID', 'header'], ['X-Correlation-ID', 'header']],
                ['effulgence.task.POST:/api/users.invoker_id', [], []],
                ['effulgence.task.default.invoker_id', ['X-Invoker-ID', 'header'], ['X-Invoker-ID', 'header']],
            ]);

        $middleware = new TaskMiddleware($task, $config);
        $request = Request::create('/api/users', 'POST');

        $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertStringContainsString('POST', $task->getResource());
        $this->assertStringContainsString('/api/users', $task->getResource());
    }
}
