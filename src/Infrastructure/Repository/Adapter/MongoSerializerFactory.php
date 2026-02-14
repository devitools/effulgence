<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Repository\Adapter;

use Constructo\Type\Collection;
use Constructo\Type\Timestamp;
use DateTime;
use DateTimeImmutable;
use Effulgence\Infrastructure\Adapter\SerializerFactory;
use Effulgence\Infrastructure\Repository\Formatter\MongoArrayToEntity;
use Effulgence\Infrastructure\Repository\Formatter\MongoDateTimeToEntity;
use Effulgence\Infrastructure\Repository\Formatter\MongoTimestampToEntity;

class MongoSerializerFactory extends SerializerFactory
{
    protected function converters(): array
    {
        return [
            Timestamp::class => new MongoTimestampToEntity(),
            DateTime::class => new MongoDateTimeToEntity(),
            DateTimeImmutable::class => new MongoDateTimeToEntity(),
            Collection::class => new MongoArrayToEntity(),
            'array' => new MongoArrayToEntity(),
        ];
    }
}
