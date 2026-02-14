<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Repository;

use Effulgence\Infrastructure\Repository\MongoRepository;

class MongoRepositoryTestMock extends MongoRepository
{
    protected function resource(): string
    {
        return 'x';
    }
}
