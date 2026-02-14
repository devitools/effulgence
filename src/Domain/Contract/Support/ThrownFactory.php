<?php

declare(strict_types=1);

namespace Effulgence\Domain\Contract\Support;

use DateTimeImmutable;
use Effulgence\Domain\Exception\Parser\Thrown;
use Throwable;

interface ThrownFactory
{
    public function make(Throwable $throwable, DateTimeImmutable $at = new DateTimeImmutable()): Thrown;
}
