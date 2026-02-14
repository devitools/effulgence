<?php

declare(strict_types=1);

namespace Effulgence\Test\Testing\Stub\Type;

enum BackedEnumeration: string
{
    case FOO = 'foo';
    case BAR = 'bar';
    case BAZ = 'baz';
}
