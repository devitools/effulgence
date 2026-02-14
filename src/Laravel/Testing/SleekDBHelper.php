<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Testing;

use Constructo\Core\Fake\Faker;
use Constructo\Support\Set;
use ReflectionException;
use Effulgence\Infrastructure\Adapter\DeserializerFactory;
use Effulgence\Infrastructure\Adapter\SerializerFactory;
use Effulgence\Infrastructure\Database\Document\SleekDBFactory;
use Effulgence\Testing\Resource\AbstractHelper;

use function Constructo\Cast\arrayify;

final class SleekDBHelper extends AbstractHelper
{
    public function __construct(
        Faker $faker,
        SerializerFactory $serializerFactory,
        DeserializerFactory $deserializerFactory,
        private readonly SleekDBFactory $factory,
    ) {
        parent::__construct($faker, $serializerFactory, $deserializerFactory);
    }

    public function truncate(string $resource): void
    {
        $database = $this->factory->make($resource);
        $database->deleteBy(
            [
                '_id',
                '>=',
                0,
            ]
        );
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @throws ReflectionException
     */
    public function seed(string $type, string $resource, array $override = []): Set
    {
        $data = $this->fake($type, $override);

        $generated = $this->factory->make($resource)
            ->insert($data);
        return new Set(array_merge($data, $generated));
    }

    public function count(string $resource, array $filters = []): int
    {
        $database = $this->factory->make($resource);
        $array = arrayify($database->findBy($filters));
        return count($array);
    }
}
