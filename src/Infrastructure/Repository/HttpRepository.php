<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Repository;

use Constructo\Contract\Message;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Effulgence\Domain\Contract\Support\ThrownFactory;
use Effulgence\Domain\Event\RequestExecutedEvent;
use Effulgence\Domain\Exception\RepositoryException;
use Effulgence\Infrastructure\Http\Received;

use function Constructo\Json\encode;
use function Effulgence\Runtime\dispatch;

abstract class HttpRepository
{
    private readonly ClientInterface $client;

    private readonly array $options;

    private readonly ThrownFactory $thrownFactory;

    public function __construct(
        ?ClientInterface $client = null,
        ?ThrownFactory $thrownFactory = null,
    ) {
        $this->options = $this->options();
        $this->client = $client ?? new Client($this->options);
        $this->thrownFactory = $thrownFactory ?? app(ThrownFactory::class);
    }

    abstract protected function options(): array;

    /**
     * @throws RepositoryException
     */
    protected function request(string $method = 'POST', string $uri = '', array $options = []): Message
    {
        $message = null;
        try {
            $response = $this->client->request($method, $uri, $options);
            $message = $this->format($response);
        } catch (GuzzleException $exception) {
            $message = $this->extract($exception);
            throw new RepositoryException(static::class, $exception);
        } finally {
            $this->dispatch($options, $method, $uri, $message);
        }
        return $message;
    }

    private function extractHeaders(?ResponseInterface $response): array
    {
        return array_map(
            fn (array $item) => count($item) === 1
                ? $item[0]
                : $item,
            $response?->getHeaders() ?? []
        );
    }

    private function extractBody(?ResponseInterface $response): ?string
    {
        $body = $response?->getBody();
        return $body?->getContents();
    }

    private function format(ResponseInterface $response): Message
    {
        $headers = $this->extractHeaders($response);
        $content = $this->extractBody($response);
        return new Received($headers, $content);
    }

    private function extract(GuzzleException $exception): Message
    {
        $headers = [];
        $content = null;
        if ($exception instanceof ClientException) {
            $response = $exception->getResponse();
            $headers = $this->extractHeaders($response);
            $content = $this->extractBody($response);
        }
        $thrown = $this->thrownFactory->make($exception);
        $headers['X-Exception'] = encode($thrown->context());
        return new Received($headers, $content);
    }

    private function dispatch(array $options, string $method, string $uri, ?Message $message): void
    {
        $options = array_merge($this->options, $options);
        $event = new RequestExecutedEvent($method, $uri, $options, $message);
        dispatch($event);
    }
}
