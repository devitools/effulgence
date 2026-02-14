<?php

declare(strict_types=1);

namespace Effulgence\Infrastructure\Http;

enum ResponseType
{
    case SUCCESS;
    case ERROR;
    case FAIL;
}
