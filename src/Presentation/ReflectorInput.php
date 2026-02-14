<?php

declare(strict_types=1);

namespace Effulgence\Presentation;

use Constructo\Factory\ReflectorFactory;
use Constructo\Factory\SchemaFactory;
use Constructo\Support\Metadata\Schema;
use ReflectionException;

abstract class ReflectorInput extends Input
{
    protected Schema $schema;

    protected ?string $source = null;

    /**
     * @throws ReflectionException
     */
    public function prepareForValidation(): void
    {
        $schemaFactory = app(SchemaFactory::class);
        $factory = app(ReflectorFactory::class);
        $this->schema = $this->setup($schemaFactory, $factory);
    }

    /**
     * @throws ReflectionException
     */
    protected function setup(SchemaFactory $schemaFactory, ReflectorFactory $factory): Schema
    {
        $schema = $this->make($schemaFactory, $factory);
        return $this->using($schema);
    }

    /**
     * @return array<string, array|string>
     */
    final public function rules(): array
    {
        return $this->schema->rules();
    }

    /**
     * @return array<string, callable(mixed):mixed|string>
     */
    final public function mappings(): array
    {
        return $this->schema->mappings();
    }

    protected function using(Schema $schema): Schema
    {
        return $schema;
    }

    protected function fallback(array $data, string $field): ?string
    {
        return isset($data[$field])
            ? $field
            : null;
    }

    /**
     * @throws ReflectionException
     */
    private function make(SchemaFactory $schemaFactory, ReflectorFactory $factory): Schema
    {
        if ($this->source === null || ! class_exists($this->source)) {
            return $schemaFactory->make();
        }
        $reflector = $factory->make();
        /** @var class-string<object> $source */
        $source = $this->source;
        return $reflector->reflect($source);
    }
}
