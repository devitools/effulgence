<?php

declare(strict_types=1);

namespace Effulgence\Domain\Exception;

enum ThrowableType: string
{
    case INVALID_INPUT = 'invalid_input';
    case RETRY_AVAILABLE = 'retry_available';
    case FALLBACK_REQUIRED = 'fallback_required';
    case UNRECOVERABLE = 'unrecoverable';
    case UNTREATED = 'untreated';
}
