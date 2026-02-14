<?php

declare(strict_types=1);

namespace Effulgence\Test\Testing;

use PHPUnit\Framework\TestCase;

abstract class ExtensibleCase extends TestCase
{
    private array $callbacks = [];

    protected function tearDown(): void
    {
        gc_collect_cycles();

        foreach ($this->callbacks as $callback) {
            $callback();
        }
        parent::tearDown();
    }

    protected function registerTearDown(callable $callback): void
    {
        $this->callbacks[] = $callback;
    }
}
