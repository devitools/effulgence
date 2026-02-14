<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Repository\Adapter;

use Constructo\Type\Collection;
use Effulgence\Infrastructure\Adapter\SerializerFactory;
use Effulgence\Infrastructure\Repository\Formatter\RelationalJsonToArray;

class RelationalSerializerFactory extends SerializerFactory
{
    protected function converters(): array
    {
        return [
            Collection::class => new RelationalJsonToArray(),
            'array' => new RelationalJsonToArray(),
        ];
    }
}
