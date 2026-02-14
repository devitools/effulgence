<?php

declare(strict_types=1);

namespace Effulgence\Test\Presentation\Output\Fail;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Effulgence\Presentation\Output\Fail\BadRequest;
use Effulgence\Presentation\Output\Fail\Conflict;
use Effulgence\Presentation\Output\Fail\ExpectationFailed;
use Effulgence\Presentation\Output\Fail\FailedDependency;
use Effulgence\Presentation\Output\Fail\Forbidden;
use Effulgence\Presentation\Output\Fail\Gone;
use Effulgence\Presentation\Output\Fail\LengthRequired;
use Effulgence\Presentation\Output\Fail\Locked;
use Effulgence\Presentation\Output\Fail\MethodNotAllowed;
use Effulgence\Presentation\Output\Fail\Misdirected;
use Effulgence\Presentation\Output\Fail\PayloadTooLarge;
use Effulgence\Presentation\Output\Fail\PaymentRequired;
use Effulgence\Presentation\Output\Fail\PreconditionFailed;
use Effulgence\Presentation\Output\Fail\PreconditionRequired;
use Effulgence\Presentation\Output\Fail\PropertiesAreTooLarge;
use Effulgence\Presentation\Output\Fail\ProxyAuthenticationRequired;
use Effulgence\Presentation\Output\Fail\RangeNotSatisfiable;
use Effulgence\Presentation\Output\Fail\RequestTimeout;
use Effulgence\Presentation\Output\Fail\TooEarly;
use Effulgence\Presentation\Output\Fail\TooMany;
use Effulgence\Presentation\Output\Fail\Unauthorized;
use Effulgence\Presentation\Output\Fail\UnavailableForLegalReasons;
use Effulgence\Presentation\Output\Fail\UnprocessableEntity;
use Effulgence\Presentation\Output\Fail\UnsupportedMediaType;
use Effulgence\Presentation\Output\Fail\UpdateRequired;

final class FailTest extends TestCase
{
    #[DataProvider('failClassesProvider')]
    public function testFailClassesWithStringContent(string $className): void
    {
        $content = 'Validation failed';
        $properties = ['error_code' => 422];

        $instance = $className::createFrom($content, $properties);

        $this->assertEquals($content, $instance->content());
        $this->assertEquals(
            $properties,
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('failClassesProvider')]
    public function testFailClassesWithArrayContent(string $className): void
    {
        $content = [
            'errors' => [
                ['field' => 'email', 'message' => 'Invalid email format'],
                ['field' => 'password', 'message' => 'Too short'],
            ],
        ];

        $instance = $className::createFrom($content);

        $this->assertEquals($content, $instance->content());
        $this->assertEquals(
            [],
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('failClassesProvider')]
    public function testFailClassesWithIntegerContent(string $className): void
    {
        $content = 4567;

        $instance = $className::createFrom($content);

        $this->assertEquals($content, $instance->content());
        $this->assertEquals(
            [],
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('failClassesProvider')]
    public function testFailClassesWithNullContent(string $className): void
    {
        $properties = [
            'reason' => 'Authentication failed',
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $instance = $className::createFrom(null, $properties);

        $this->assertNull($instance->content());
        $this->assertEquals(
            $properties,
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('failClassesProvider')]
    public function testFailClassesWithProperties(string $className): void
    {
        $content = 'Validation failed';
        $properties = [
            'errors' => [
                'field1' => ['message' => 'Required'],
                'field2' => ['message' => 'Invalid format'],
            ],
            'request_id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
        ];

        $instance = $className::createFrom($content, $properties);

        $this->assertEquals($content, $instance->content());
        $this->assertEquals(
            $properties,
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    /**
     * @return array<string, array{class-string}>
     */
    public static function failClassesProvider(): array
    {
        return [
            'BadRequest' => [BadRequest::class],
            'Conflict' => [Conflict::class],
            'ExpectationFailed' => [ExpectationFailed::class],
            'FailedDependency' => [FailedDependency::class],
            'Forbidden' => [Forbidden::class],
            'Gone' => [Gone::class],
            'LengthRequired' => [LengthRequired::class],
            'Locked' => [Locked::class],
            'MethodNotAllowed' => [MethodNotAllowed::class],
            'Misdirected' => [Misdirected::class],
            'PayloadTooLarge' => [PayloadTooLarge::class],
            'PaymentRequired' => [PaymentRequired::class],
            'PreconditionFailed' => [PreconditionFailed::class],
            'PreconditionRequired' => [PreconditionRequired::class],
            'ProxyAuthenticationRequired' => [ProxyAuthenticationRequired::class],
            'RangeNotSatisfiable' => [RangeNotSatisfiable::class],
            'PropertiesAreTooLarge' => [PropertiesAreTooLarge::class],
            'RequestTimeout' => [RequestTimeout::class],
            'TooEarly' => [TooEarly::class],
            'TooMany' => [TooMany::class],
            'Unauthorized' => [Unauthorized::class],
            'UnavailableForLegalReasons' => [UnavailableForLegalReasons::class],
            'UnprocessableEntity' => [UnprocessableEntity::class],
            'UnsupportedMediaType' => [UnsupportedMediaType::class],
            'UpdateRequired' => [UpdateRequired::class],
        ];
    }
}
