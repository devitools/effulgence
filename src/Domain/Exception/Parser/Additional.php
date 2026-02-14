<?php

declare(strict_types=1);

namespace Effulgence\Domain\Exception\Parser;

readonly class Additional
{
    public function __construct(
        public string $line,
        public mixed $body,
        public array $headers,
        public array $query,
        public string $message,
        public Thrown $thrown,
        public array $errors
    ) {
    }

    public function context(): array
    {
        return [
            'line' => $this->line,
            'body' => $this->body,
            'headers' => $this->headers,
            'query' => $this->query,
            'message' => $this->message,
            'thrown' => $this->thrown->context(),
            'errors' => $this->errors,
        ];
    }
}
