<?php

declare(strict_types=1);

namespace Effulgence\Domain\Exception;

use Exception;
use Throwable;

class InvalidInputException extends Exception
{
    /**
     * @param array<string,string> $errors
     */
    public function __construct(
        private readonly array $errors,
        ?Throwable $previous = null,
        int $code = 0
    ) {
        $message = sprintf('Detected %s errors: "%s"', count($errors), implode('", "', $errors));
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<string,string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
