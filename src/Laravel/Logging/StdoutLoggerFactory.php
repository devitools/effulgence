<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Logging;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Effulgence\Infrastructure\Logging\StdoutLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;

readonly class StdoutLoggerFactory
{
    public function __construct(private ConfigRepository $config)
    {
    }

    public function __invoke(array $config): LoggerInterface
    {
        $env = $config['env'] ?? 'dev';
        return $this->make($env);
    }

    public function make(string $env = 'dev'): LoggerInterface
    {
        $output = new ConsoleOutput();
        $default = [
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::EMERGENCY,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG,
        ];
        $levels = arrayify($this->config->get('effulgence.logger.default.levels', $default));
        $format = stringify($this->config->get('effulgence.logger.default.format', '[{{env}}.{{level}}] {{message}}: {{context}}'));
        return new StdoutLogger($output, $levels, $format, $env);
    }
}
