<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Database\Document;

use SleekDB\Store;

interface SleekDBFactory
{
    public function make(string $resource): Store;
}
