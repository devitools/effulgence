<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Testing\Mock;

use Closure;
use PHPUnit\Framework\Constraint\Constraint;
use Effulgence\Laravel\Testing\Extension\LoggerExtension;
use Effulgence\Testing\FailException;

final class LoggerExtensionMock
{
    use LoggerExtension;

    private static ?Closure $assertion = null;

    public function __construct(?Closure $assertion = null)
    {
        if ($assertion !== null) {
            self::$assertion = $assertion;
        }
    }

    public function exposeSetUpLogger(): void
    {
        $this->setUpLogger();
    }

    public function exposeTearDownLogger(): void
    {
        $this->tearDownLogger();
    }

    public function exposeAssertLogged(?string $pattern = null, ?string $level = null): void
    {
        $this->assertLogged($pattern, $level);
    }

    public function exposeTally(?string $pattern, ?string $level): int
    {
        return $this->tally($pattern, $level);
    }

    public function getIsLoggerSetup(): bool
    {
        return $this->isLoggerSetup;
    }

    /**
     * @throws FailException
     */
    public static function fail(string $message = ''): never
    {
        throw new FailException(
            $message
                ?: 'Test failure'
        );
    }

    public static function assertThat(mixed $value, Constraint $constraint, string $message = ''): void
    {
        if (self::$assertion !== null) {
            call_user_func(self::$assertion, $value, $constraint, $message);
        }
    }
}
