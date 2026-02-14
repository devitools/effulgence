<?php

declare(strict_types=1);

namespace Effulgence\Test\Testing\Stub;

use Effulgence\Test\Testing\Stub\Type\BackedEnumeration;

class Stub
{
    public function __construct(
        public readonly string $foo,
        public readonly int $bar,
        public readonly BackedEnumeration $baz = BackedEnumeration::BAZ,
    ) {
    }
}
