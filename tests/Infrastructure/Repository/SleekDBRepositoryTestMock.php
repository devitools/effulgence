<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Repository;

use Effulgence\Infrastructure\Repository\SleekDBRepository;

class SleekDBRepositoryTestMock extends SleekDBRepository
{
    protected function resource(): string
    {
        return 'x';
    }
}
