<?php

declare(strict_types=1);

namespace Effulgence\Runtime;

use Illuminate\Support\Facades\Event;

if (! function_exists(__NAMESPACE__ . '\invoke')) {
    function invoke(callable $callback, mixed ...$args): mixed
    {
        return $callback(...$args);
    }
}

if (! function_exists(__NAMESPACE__ . '\dispatch')) {
    function dispatch(object $event): void
    {
        Event::dispatch($event);
    }
}
