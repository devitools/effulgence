<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Repository;

use Constructo\Contract\Message;
use Effulgence\Infrastructure\Repository\HttpRepository;

class HttpRepositoryTestMock extends HttpRepository
{
    public function exposeRequest(string $method = 'POST', string $uri = '', array $options = []): Message
    {
        return $this->request($method, $uri, $options);
    }

    protected function options(): array
    {
        return [
            'base_uri' => 'http://fake',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];
    }
}
