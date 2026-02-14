<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Event;

use Illuminate\Http\Request;
use Throwable;

class HttpHandleInterrupted
{
    public function __construct(
        public readonly Request $request,
        public readonly Throwable $exception,
    ) {
    }
}
