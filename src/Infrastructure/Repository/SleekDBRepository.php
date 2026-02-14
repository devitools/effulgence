<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Repository;

use Effulgence\Infrastructure\Database\Document\SleekDBFactory;
use Effulgence\Infrastructure\Database\Managed;
use SleekDB\Store;

/**
 * @template T of object
 * @extends Repository<T>
 */
abstract class SleekDBRepository extends Repository
{
    protected readonly Store $store;

    public function __construct(
        protected readonly Managed $managed,
        SleekDBFactory $storeFactory,
    ) {
        $this->store = $storeFactory->make($this->resource());
    }

    abstract protected function resource(): string;
}
