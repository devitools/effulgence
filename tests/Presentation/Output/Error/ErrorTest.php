<?php

declare(strict_types=1);

namespace Effulgence\Test\Presentation\Output\Error;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Effulgence\Presentation\Output\Error\BadGateway;
use Effulgence\Presentation\Output\Error\GatewayTimeout;
use Effulgence\Presentation\Output\Error\InsufficientStorage;
use Effulgence\Presentation\Output\Error\InternalServerError;
use Effulgence\Presentation\Output\Error\LoopDetected;
use Effulgence\Presentation\Output\Error\NetworkAuthenticationRequired;
use Effulgence\Presentation\Output\Error\NotImplemented;
use Effulgence\Presentation\Output\Error\ProtocolVersionNotSupported;
use Effulgence\Presentation\Output\Error\ServiceUnavailable;
use Effulgence\Presentation\Output\Error\VariantAlsoNegotiates;

final class ErrorTest extends TestCase
{
    #[DataProvider('errorClassesProvider')]
    public function testErrorClassesWithStringContent(string $className): void
    {
        $content = 'An internal server error occurred';
        $properties = ['trace_id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479'];

        $instance = $className::createFrom($content, $properties);

        $this->assertEquals($content, $instance->content());
        $this->assertEquals(
            $properties,
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('errorClassesProvider')]
    public function testErrorClassesWithIntegerContent(string $className): void
    {
        $content = 503;

        $instance = $className::createFrom($content);

        $this->assertEquals($content, $instance->content());
        $this->assertEquals(
            [],
            $instance->properties()
                ->toArray()
        );
        $this->assertInstanceOf($className, $instance);
    }

    #[DataProvider('errorClassesProvider')]
    public function testErrorClassesWithNullContent(string $className): void
    {
        $properties = [
            'timestamp' => date('Y-m-d H:i:s'),
            'server' => 'api-server-01',
            'request_id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
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

    #[DataProvider('errorClassesProvider')]
    public function testErrorClassesWithDetailedProperties(string $className): void
    {
        $content = 'System temporarily unavailable';
        $properties = [
            'trace_id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
            'timestamp' => date('Y-m-d H:i:s'),
            'details' => [
                'file' => 'PaymentProcessor.php',
                'line' => 423,
                'context' => [
                    'user_id' => 12345,
                    'action' => 'process_payment',
                ],
            ],
            'retry_after' => 300,
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
    public static function errorClassesProvider(): array
    {
        return [
            'InternalServerError' => [InternalServerError::class],
            'BadGateway' => [BadGateway::class],
            'InsufficientStorage' => [InsufficientStorage::class],
            'ServiceUnavailable' => [ServiceUnavailable::class],
            'ProtocolVersionNotSupported' => [ProtocolVersionNotSupported::class],
            'VariantAlsoNegotiates' => [VariantAlsoNegotiates::class],
            'NotImplemented' => [NotImplemented::class],
            'GatewayTimeout' => [GatewayTimeout::class],
            'NetworkAuthenticationRequired' => [NetworkAuthenticationRequired::class],
            'LoopDetected' => [LoopDetected::class],
        ];
    }
}
