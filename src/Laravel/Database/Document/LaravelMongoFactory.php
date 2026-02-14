<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Database\Document;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use MongoDB\Client;
use MongoDB\Collection;
use Effulgence\Infrastructure\Database\Document\MongoFactory;

use function Constructo\Cast\stringify;

readonly class LaravelMongoFactory implements MongoFactory
{
    public function __construct(private ConfigRepository $config)
    {
    }

    public function make(string $resource): Collection
    {
        $uri = stringify($this->config->get('effulgence.databases.mongo.uri'));
        $database = stringify($this->config->get('effulgence.databases.mongo.database'));
        return (new Client($uri))
            ->selectDatabase($database)
            ->selectCollection($resource);
    }
}
