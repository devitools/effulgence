<?php

declare(strict_types=1);

namespace Effulgence\Test\_;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testShouldRequireFunctions(): void
    {
        $files = [
            'src/_/runtime.php',
        ];
        foreach ($files as $file) {
            $filename = __DIR__ . '/../../' . $file;
            $this->assertFileExists($filename, sprintf("File '%s' does not exist", $file));
            require_once $filename;
        }
    }
}
