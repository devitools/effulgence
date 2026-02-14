<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Request;

use Constructo\Support\Set;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Psr\EventDispatcher\EventDispatcherInterface;
use Effulgence\Domain\Event\ValidationFailedEvent;

use function Constructo\Cast\stringify;

abstract class LaravelFormRequest extends FormRequest
{
    protected Set $properties;

    protected Set $values;

    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        mixed $content = null,
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->properties = new Set([]);
        $this->values = new Set([]);
    }

    final public function properties(): Set
    {
        $headers = $this->headers->all();
        $headers = $this->normalizeHeaders($headers);
        return $this->properties->along($headers);
    }

    final public function values(): Set
    {
        return $this->values->along($this->validated());
    }

    /**
     * @template T of mixed
     * @param T $default
     *
     * @return T
     */
    final public function value(string $key, mixed $default = null): mixed
    {
        return $this->retrieve($this->values(), $key, $default);
    }

    protected function failedValidation(Validator $validator): void
    {
        $resource = sprintf('http::%s:%s', strtoupper($this->getMethod()), $this->getRequestUri());
        $values = Set::createFrom($this->all());
        $message = stringify($validator->errors());

        $dispatcher = app(EventDispatcherInterface::class);
        $dispatcher->dispatch(new ValidationFailedEvent($resource, $values, $message));

        parent::failedValidation($validator);
    }

    protected function retrieve(Set $data, string $key, mixed $default = null): mixed
    {
        return data_get($data->toArray(), $key, $default);
    }

    private function normalizeHeaders(array $headers): array
    {
        $callback = fn (mixed $value) => is_array($value)
            ? implode('; ', $value)
            : $value;
        return array_map($callback, $headers);
    }
}
