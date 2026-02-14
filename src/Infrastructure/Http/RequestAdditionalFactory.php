<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Http;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Effulgence\Domain\Exception\InvalidInputException;
use Effulgence\Domain\Exception\Parser\Additional;
use Effulgence\Domain\Exception\Parser\DefaultThrownFactory;
use Throwable;

use function array_map;
use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;
use function implode;
use function is_array;

class RequestAdditionalFactory
{
    public function __construct(private readonly DefaultThrownFactory $factory)
    {
    }

    public function make(Request $request, Throwable $throwable): Additional
    {
        $thrown = $this->factory->make($throwable);
        $errors = match (true) {
            $throwable instanceof ValidationException => $throwable->validator->errors()
                ->getMessages(),
            $throwable instanceof InvalidInputException => $throwable->getErrors(),
            default => [],
        };
        return new Additional(
            line: sprintf('%s %s', $request->getMethod(), $request->getUri()),
            body: $request->all(),
            headers: $this->headers($request),
            query: $request->query(),
            message: $thrown->resume(),
            thrown: $thrown,
            errors: $errors,
        );
    }

    private function headers(Request $request): array
    {
        $callback = function (mixed $header): string {
            if (! is_array($header)) {
                return stringify($header);
            }
            return implode('; ', array_map(fn (mixed $value) => stringify($value), $header));
        };
        return array_map($callback, $request->headers->all());
    }
}
