<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Repository\Adapter;

use Constructo\Type\Timestamp;
use DateTime;
use DateTimeImmutable;
use Effulgence\Infrastructure\Adapter\DeserializerFactory;
use Effulgence\Infrastructure\Repository\Formatter\MongoDateTimeToDatabase;
use Effulgence\Infrastructure\Repository\Formatter\MongoTimestampToDatabase;

class MongoDeserializerFactory extends DeserializerFactory
{
    protected function formatters(): array
    {
        return [
            Timestamp::class => new MongoTimestampToDatabase(),
            DateTime::class => new MongoDateTimeToDatabase(),
            DateTimeImmutable::class => new MongoDateTimeToDatabase(),
        ];
    }
}
