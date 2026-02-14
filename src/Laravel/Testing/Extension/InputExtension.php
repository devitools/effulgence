<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Testing\Extension;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Effulgence\Presentation\Input;

/**
 * @phpstan-ignore trait.unused
 */
trait InputExtension
{
    private bool $isRequestSetUp = false;

    abstract public static function fail(string $message = ''): never;

    protected function setUpInput(): void
    {
        $this->isRequestSetUp = true;
    }

    protected function tearDownInput(): void
    {
        $this->isRequestSetUp = false;
    }

    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @param array<string, array<string>|string> $headers
     * @param array<string, mixed> $args
     * @return T
     */
    final protected function input(
        string $class,
        array $parsedBody = [],
        array $queryParams = [],
        array $params = [],
        array $headers = [],
        array $args = [],
    ): mixed {
        if ($this->isRequestSetUp) {
            $this->setUpRequestContext($parsedBody, $queryParams, $params, $headers);
            return $this->make($class, $args);
        }
        static::fail('Request is not set up.');
    }

    /**
     * @param array<string, array<string>|string> $headers
     */
    final protected function setUpRequestContext(
        array $parsedBody = [],
        array $queryParams = [],
        array $params = [],
        array $headers = [],
        string $method = 'POST',
        string $uri = '/',
    ): void {
        $request = Request::create($uri, $method, array_merge($parsedBody, $queryParams), [], [], [], json_encode($parsedBody));
        $request->headers->add($headers);

        foreach ($params as $key => $value) {
            $request->route()?->setParameter($key, $value);
        }

        app()->instance('request', $request);
    }

    protected function invoke(callable $action, Input $input, array $values = [], int $columns = 80): mixed
    {
        try {
            return $action($input);
        } catch (ValidationException $exception) {
            $message = sprintf(
                "[%s] %s \n%s\n%s",
                $exception->getMessage(),
                json_encode(
                    $exception->validator->errors()
                        ->getMessages(),
                    JSON_PRETTY_PRINT
                ),
                str_repeat('#', $columns),
                json_encode($values, JSON_PRETTY_PRINT),
            );
            static::fail($message);
        }
    }

    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     *
     * @return T
     */
    abstract protected function make(string $class, array $args = []): mixed;
}
