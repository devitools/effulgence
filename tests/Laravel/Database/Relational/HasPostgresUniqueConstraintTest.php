<?php

declare(strict_types=1);

namespace Effulgence\Test\Laravel\Database\Relational;

use PHPUnit\Framework\TestCase;
use Effulgence\Domain\Exception\UniqueKeyViolationException;
use Effulgence\Laravel\Database\Relational\Support\HasPostgresUniqueConstraint;
use RuntimeException;

final class HasPostgresUniqueConstraintTest extends TestCase
{
    use HasPostgresUniqueConstraint;

    public function testShouldDetectUniqueConstraintViolation(): void
    {
        $exception = new RuntimeException(
            'SQLSTATE[23505]: Unique violation: 7 ERROR: duplicate key value violates unique constraint "users_email_unique" DETAIL: Key (email)=(test@test.com) already exists.'
        );

        $result = $this->detectUniqueKeyViolation($exception);

        $this->assertInstanceOf(UniqueKeyViolationException::class, $result);
    }

    public function testShouldReturnNullForNonUniqueConstraintException(): void
    {
        $exception = new RuntimeException('Some other error');
        $result = $this->detectUniqueKeyViolation($exception);

        $this->assertNull($result);
    }
}
