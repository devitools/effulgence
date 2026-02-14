<?php

declare(strict_types=1);

namespace Effulgence\Test\Infrastructure\Exception;

use Exception;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Effulgence\Domain\Exception\InvalidInputException;
use Effulgence\Domain\Exception\Parser\DefaultThrownFactory;
use Effulgence\Domain\Exception\Parser\Thrown;
use Effulgence\Infrastructure\Http\RequestAdditionalFactory;
use Symfony\Component\HttpFoundation\HeaderBag;
use Throwable;

final class AdditionalFactoryTest extends TestCase
{
    private RequestAdditionalFactory $additionalFactory;

    protected function setUp(): void
    {
        $this->additionalFactory = new RequestAdditionalFactory(new DefaultThrownFactory());
    }

    #[TestWith([new Exception('Test exception')])]
    #[TestWith([new InvalidInputException(['error'])])]
    public function testShouldCreateAdditionalFactory(Throwable $throwable): void
    {
        $request = Request::create('/api/test', 'POST', [], [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => '*/*',
        ], json_encode(['key' => 'value']));
        $request->request->replace(['key' => 'value']);

        $additional = $this->additionalFactory->make($request, $throwable);

        $thrown = Thrown::createFrom($throwable);
        $errors = match (true) {
            $throwable instanceof InvalidInputException => $throwable->getErrors(),
            default => [],
        };

        $this->assertStringContainsString('POST', $additional->line);
        $this->assertStringContainsString('/api/test', $additional->line);
        $this->assertEquals($thrown->resume(), $additional->message);
        $this->assertEquals($errors, $additional->errors);
    }
}
