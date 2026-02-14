<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Listener;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Psr\Log\LoggerInterface;
use Effulgence\Infrastructure\Http\RequestAdditionalFactory;
use Effulgence\Laravel\Event\HttpHandleInterrupted;
use Effulgence\Laravel\Event\HttpHandleStarted;

use function Constructo\Cast\arrayify;
use function Constructo\Cast\boolify;

class SentryHttpListener
{
    private readonly array $options;

    private readonly bool $debug;

    private bool $initialized = false;

    public function __construct(
        private readonly ConfigRepository $config,
        private readonly LoggerInterface $logger,
        private readonly RequestAdditionalFactory $factory,
    ) {
        $this->options = arrayify($this->config->get('effulgence.sentry.options'));
        $this->debug = boolify($this->config->get('effulgence.sentry.debug', false));
    }

    public function handleStarted(HttpHandleStarted $event): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        if (! $this->initialized && function_exists('Sentry\init')) {
            \Sentry\init($this->options);
            $this->initialized = true;
            if ($this->debug) {
                $this->logger->debug('Sentry initialized', $this->options);
            }
        }
    }

    public function handleInterrupted(HttpHandleInterrupted $event): void
    {
        if (! $this->isEnabled() || ! function_exists('Sentry\captureException')) {
            return;
        }

        $additional = $this->factory->make($event->request, $event->exception);
        $context = $additional->context();

        if (function_exists('Sentry\configureScope')) {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($additional, $context): void {
                foreach ($context as $key => $value) {
                    $scope->setExtra($key, $value);
                }
                $scope->setExtra('details', $additional->message);
            });
        }

        if ($this->debug) {
            $this->logger->debug('Sentry captured exception', ['exception' => $additional->message]);
        }
        \Sentry\captureException($event->exception);
    }

    private function isEnabled(): bool
    {
        return isset($this->options['dsn']);
    }
}
