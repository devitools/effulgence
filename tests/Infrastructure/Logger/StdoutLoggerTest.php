<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Logger;

use PHPUnit\Framework\TestCase;
use Effulgence\Infrastructure\Logging\StdoutLogger;
use stdClass;
use Symfony\Component\Console\Output\OutputInterface;

final class StdoutLoggerTest extends TestCase
{
    private OutputInterface $output;

    private StdoutLogger $stdoutLogger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->output = $this->createMock(OutputInterface::class);
        $levels = [
            'debug',
            'info',
            'notice',
            'warning',
            'error',
            'critical',
            'alert',
            'emergency',
        ];

        $this->stdoutLogger = new StdoutLogger(
            output: $this->output,
            levels: $levels,
            format: '[{{env}}.{{level}}] {{message}}: {{context}}',
            env: 'test'
        );
    }

    public function testShouldLogInfoMessageCorrectly(): void
    {
        $message = 'Test Message';
        $context = ['key' => 'value'];
        $expectedOutput = "[test.info] Test Message: ['key' => 'value']";

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($expectedOutput);

        $this->stdoutLogger->info($message, $context);
    }

    public function testShouldLogDebugMessageCorrectly(): void
    {
        $message = 'Debug Message';
        $context = ['debug_key' => 'debug_value'];
        $expectedOutput = "[test.debug] Debug Message: ['debug_key' => 'debug_value']";

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($expectedOutput);

        $this->stdoutLogger->debug($message, $context);
    }

    public function testShouldLogErrorMessageCorrectly(): void
    {
        $message = 'Error Message';
        $context = ['error_code' => 500];
        $expectedOutput = "[test.error] Error Message: ['error_code' => 500]";

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($expectedOutput);

        $this->stdoutLogger->error($message, $context);
    }

    public function testShouldNotLogWhenLevelNotIncluded(): void
    {
        $message = 'Test Message';
        $context = ['key' => 'value'];
        $limitedLevels = ['error', 'critical'];

        $stdoutLogger = new StdoutLogger(
            output: $this->output,
            levels: $limitedLevels,
            format: '[{{env}}.{{level}}] {{message}}: {{content}}',
            env: 'test'
        );

        $this->output->expects($this->never())
            ->method('writeln');

        $stdoutLogger->info($message, $context);
    }

    public function testShouldHandleComplexArrayContext(): void
    {
        $message = 'Complex Context';
        $context = [
            'user' => [
                'id' => 1,
                'name' => 'Test User',
            ],
        ];
        $expectedOutput = "[test.info] Complex Context: ['user' => ['id' => 1, 'name' => 'Test User']]";

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($expectedOutput);

        $this->stdoutLogger->info($message, $context);
    }

    public function testShouldHandleScalarAndNonScalarValues(): void
    {
        $message = 'Mixed Context';
        $object = new stdClass();
        $object->property = 'value';

        $context = [
            'string' => 'text',
            'integer' => 123,
            'boolean' => true,
            'object' => $object,
            'null' => null,
        ];

        $this->output->expects($this->once())
            ->method('writeln');

        $this->stdoutLogger->info($message, $context);
    }

    public function testShouldLogWithoutContextCorrectly(): void
    {
        $message = 'No Context';
        $expectedOutput = '[test.info] No Context: []';

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($expectedOutput);

        $this->stdoutLogger->info($message);
    }
}
