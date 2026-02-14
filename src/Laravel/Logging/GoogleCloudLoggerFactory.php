<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Logging;

use Google\Cloud\Logging\LoggingClient;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Effulgence\Domain\Support\Task;
use Effulgence\Infrastructure\Logging\GoogleCloudLogger;

use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;

readonly class GoogleCloudLoggerFactory
{
    private string $projectId;

    private string $serviceName;

    private string $format;

    private array $options;

    public function __construct(
        private Task $task,
        ConfigRepository $config,
    ) {
        $this->projectId = stringify($config->get('effulgence.logger.gcloud.project_id', 'unknown'));
        $this->serviceName = stringify($config->get('effulgence.logger.gcloud.service_name', 'unknown'));
        $this->format = stringify(
            $config->get('effulgence.logger.gcloud.format', '{{message}} | {{resource}} | {{correlation_id}} | {{invoker_id}}')
        );
        $this->options = arrayify($config->get('effulgence.logger.gcloud.options'));
    }

    public function __invoke(array $config): GoogleCloudLogger
    {
        $env = $config['env'] ?? 'dev';
        return $this->make($env);
    }

    public function make(string $env = 'dev'): GoogleCloudLogger
    {
        $logging = new LoggingClient(['projectId' => $this->projectId]);
        $driver = $logging->logger('google-cloud', $this->options);
        return new GoogleCloudLogger(
            $driver,
            $this->task,
            $this->format,
            $this->projectId,
            $this->serviceName,
            $env
        );
    }
}
