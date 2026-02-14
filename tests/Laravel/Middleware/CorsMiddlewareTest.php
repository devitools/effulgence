<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Middleware;

use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Effulgence\Laravel\Middleware\CorsMiddleware;

final class CorsMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->method('make')
            ->willReturnCallback(function ($content = '', $status = 200, array $headers = []) {
                return new Response($content, $status, $headers);
            });

        $container = Container::getInstance();
        $container->instance(ResponseFactory::class, $responseFactory);
    }

    protected function tearDown(): void
    {
        Container::setInstance(new Container());
        parent::tearDown();
    }

    public function testShouldAddCorsHeaders(): void
    {
        $config = $this->createMock(ConfigRepository::class);
        $config->method('get')
            ->with('effulgence.cors.allow_origin', '*')
            ->willReturn('https://example.com');

        $middleware = new CorsMiddleware($config);
        $request = Request::create('/api/test', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('https://example.com', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertEquals('true', $response->headers->get('Access-Control-Allow-Credentials'));
        $this->assertEquals('*', $response->headers->get('Access-Control-Allow-Methods'));
    }

    public function testShouldReturnEmptyResponseForOptions(): void
    {
        $config = $this->createMock(ConfigRepository::class);
        $config->method('get')
            ->with('effulgence.cors.allow_origin', '*')
            ->willReturn('*');

        $middleware = new CorsMiddleware($config);
        $request = Request::create('/api/test', 'OPTIONS');

        $response = $middleware->handle($request, function () {
            return new Response('Should not reach here');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('*', $response->headers->get('Access-Control-Allow-Origin'));
    }
}
