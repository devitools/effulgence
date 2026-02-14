<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Event;

use Illuminate\Http\Request;

class HttpHandleStarted
{
    public function __construct(public readonly Request $request)
    {
    }
}
