<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Repository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Effulgence\Domain\Contract\Support\ThrownFactory;
use Effulgence\Domain\Exception\Parser\Thrown;
use Effulgence\Domain\Exception\RepositoryException;

class HttpRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = Container::getInstance();
        Facade::setFacadeApplication($app);

        $app->instance('events', new class {
            public function dispatch(object $event): void
            {
            }
        });
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(new Container());
        parent::tearDown();
    }

    public function testShouldHaveContentAndProperties(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('getContents')
            ->willReturn('{"message": "Hello, World!"}');

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['Content-Type' => ['application/json']]);
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('POST', '', [])
            ->willReturn($response);

        $thrownFactory = $this->createMock(ThrownFactory::class);

        $repository = new HttpRepositoryTestMock($client, $thrownFactory);
        $response = $repository->exposeRequest();

        $this->assertEquals('{"message": "Hello, World!"}', $response->content());
        $this->assertEquals(
            'application/json',
            $response->properties()
                ->get('Content-Type')
        );
    }

    public function testShouldRaiseGeneralException(): void
    {
        $this->expectException(RepositoryException::class);

        $exception = new BadResponseException(
            'Internal Server Error',
            $this->createMock(RequestInterface::class),
            $this->createMock(ResponseInterface::class)
        );

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('POST', '', [])
            ->willThrowException($exception);

        $thrownFactory = $this->createMock(ThrownFactory::class);
        $thrownFactory->method('make')
            ->willReturn(Thrown::createFrom($exception));

        $repository = new HttpRepositoryTestMock($client, $thrownFactory);
        $repository->exposeRequest();
    }

    public function testShouldExtractHeadersWithMultipleValues(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('getContents')
            ->willReturn('{"data": "test"}');

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getHeaders')
            ->willReturn([
                'Content-Type' => ['application/json'],
                'Set-Cookie' => ['cookie1=value1', 'cookie2=value2'],
            ]);
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('GET', '/test', [])
            ->willReturn($response);

        $thrownFactory = $this->createMock(ThrownFactory::class);

        $repository = new HttpRepositoryTestMock($client, $thrownFactory);
        $message = $repository->exposeRequest('GET', '/test');

        $this->assertEquals('{"data": "test"}', $message->content());
        $this->assertEquals('application/json', $message->properties()->get('Content-Type'));
        $this->assertEquals(['cookie1=value1', 'cookie2=value2'], $message->properties()->get('Set-Cookie'));
    }

    public function testShouldHandleClientExceptionWithResponse(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn('{"error": "Bad Request"}');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getHeaders')
            ->willReturn(['Content-Type' => ['application/json']]);
        $response->method('getBody')
            ->willReturn($stream);

        $exception = new ClientException(
            'Bad Request',
            $this->createMock(RequestInterface::class),
            $response
        );

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('POST', '', [])
            ->willThrowException($exception);

        $thrownFactory = $this->createMock(ThrownFactory::class);
        $thrownFactory->expects($this->once())
            ->method('make')
            ->with($exception)
            ->willReturn(Thrown::createFrom($exception));

        try {
            $repository = new HttpRepositoryTestMock($client, $thrownFactory);
            $repository->exposeRequest();
            $this->fail('Expected RepositoryException to be thrown');
        } catch (RepositoryException $e) {
            $this->assertInstanceOf(RepositoryException::class, $e);
        }
    }

    public function testShouldHandleNonClientException(): void
    {
        $this->expectException(RepositoryException::class);

        $exception = new ConnectException(
            'Connection refused',
            $this->createMock(RequestInterface::class)
        );

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('POST', '', [])
            ->willThrowException($exception);

        $thrownFactory = $this->createMock(ThrownFactory::class);
        $thrownFactory->expects($this->once())
            ->method('make')
            ->with($exception)
            ->willReturn(Thrown::createFrom($exception));

        $repository = new HttpRepositoryTestMock($client, $thrownFactory);
        $repository->exposeRequest();
    }
}
