<?php

declare(strict_types=1);

namespace Effulgence\Presentation\Input;

use Effulgence\Presentation\Input;

abstract class Resolver
{
    protected ?Resolver $previous = null;

    public function __construct(protected readonly Input $input)
    {
    }

    final public function then(Resolver $resolver): Resolver
    {
        $resolver->previous($this);
        return $resolver;
    }

    public function resolve(array $data): array
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($data);
        }
        return $data;
    }

    final protected function previous(Resolver $previous): void
    {
        $this->previous = $previous;
    }
}
