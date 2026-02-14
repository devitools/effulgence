<?php

declare(strict_types=1);

namespace Effulgence\Domain\Support;

enum Truncate
{
    case BOTH;
    case BEFORE;
    case AFTER;
    case NONE;
}
