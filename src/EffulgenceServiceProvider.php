<?php

declare(strict_types=1);

namespace Effulgence;

use Constructo\Contract\Reflect\SpecsFactory;
use Constructo\Contract\Reflect\TypesFactory;
use Constructo\Core\Serialize\Builder;
use Constructo\Factory\ReflectorFactory;
use Constructo\Factory\SchemaFactory;
use Illuminate\Support\ServiceProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Effulgence\Domain\Contract\Support\ThrownFactory;
use Effulgence\Domain\Support\Task;
use Effulgence\Infrastructure\Adapter\Deserialize\Demolisher;
use Effulgence\Infrastructure\Adapter\Serialize\Builder as EffulgenceBuilder;
use Effulgence\Infrastructure\Database\Document\MongoFactory;
use Effulgence\Infrastructure\Database\Document\SleekDBFactory;
use Effulgence\Infrastructure\Database\Managed;
use Effulgence\Infrastructure\Database\Relational\ConnectionChecker;
use Effulgence\Infrastructure\Database\Relational\ConnectionFactory;
use Effulgence\Infrastructure\File\RulesGenerator;
use Effulgence\Infrastructure\Http\ExceptionResponseNormalizer;
use Effulgence\Infrastructure\Http\JsonFormatter;
use Effulgence\Infrastructure\Http\RequestAdditionalFactory;
use Effulgence\Infrastructure\Repository\Adapter\MongoDeserializerFactory;
use Effulgence\Infrastructure\Repository\Adapter\MongoSerializerFactory;
use Effulgence\Infrastructure\Repository\Adapter\RelationalDeserializerFactory;
use Effulgence\Infrastructure\Repository\Adapter\RelationalSerializerFactory;
use Effulgence\Laravel\Command\GenerateRules;
use Effulgence\Laravel\Database\Document\LaravelMongoFactory;
use Effulgence\Laravel\Database\Document\LaravelSleekDBFactory;
use Effulgence\Laravel\Database\Relational\LaravelConnection;
use Effulgence\Laravel\Database\Relational\LaravelConnectionChecker;
use Effulgence\Laravel\Database\Relational\LaravelConnectionFactory;
use Effulgence\Laravel\Listener\SentryHttpListener;
use Effulgence\Laravel\Support\LaravelSpecsFactory;
use Effulgence\Laravel\Support\LaravelThrownFactory;
use Effulgence\Laravel\Support\LaravelTypesFactory;

class EffulgenceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/effulgence.php', 'effulgence');

        $this->registerCoreBindings();
        $this->registerDatabaseBindings();
        $this->registerAdapterBindings();
        $this->registerHttpBindings();
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/effulgence.php' => config_path('effulgence.php'),
        ], 'effulgence-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateRules::class,
            ]);
        }

        $this->registerEventListeners();
    }

    private function registerCoreBindings(): void
    {
        $this->app->singleton(Task::class);

        $this->app->singleton(TypesFactory::class, LaravelTypesFactory::class);
        $this->app->singleton(SpecsFactory::class, LaravelSpecsFactory::class);
        $this->app->singleton(ThrownFactory::class, LaravelThrownFactory::class);

        $this->app->singleton(EffulgenceBuilder::class);
        $this->app->singleton(Builder::class, fn ($app) => $app->make(EffulgenceBuilder::class));
        $this->app->singleton(Demolisher::class);

        $this->app->singleton(Managed::class);

        $this->app->singleton(ReflectorFactory::class);
        $this->app->singleton(SchemaFactory::class);
        $this->app->singleton(RulesGenerator::class);
    }

    private function registerDatabaseBindings(): void
    {
        $this->app->singleton(ConnectionFactory::class, LaravelConnectionFactory::class);
        $this->app->singleton(MongoFactory::class, LaravelMongoFactory::class);
        $this->app->singleton(SleekDBFactory::class, LaravelSleekDBFactory::class);

        $this->app->singleton(LaravelConnection::class, function ($app) {
            $factory = $app->make(ConnectionFactory::class);
            $connection = $app['config']->get('database.default', 'pgsql');
            return $factory->make($connection);
        });

        $this->app->singleton(ConnectionChecker::class, LaravelConnectionChecker::class);
        $this->app->singleton(LaravelConnectionChecker::class);
    }

    private function registerAdapterBindings(): void
    {
        $this->app->singleton(RelationalSerializerFactory::class);
        $this->app->singleton(RelationalDeserializerFactory::class);
        $this->app->singleton(MongoSerializerFactory::class);
        $this->app->singleton(MongoDeserializerFactory::class);
    }

    private function registerHttpBindings(): void
    {
        $this->app->singleton(JsonFormatter::class);
        $this->app->singleton(ExceptionResponseNormalizer::class);
        $this->app->singleton(RequestAdditionalFactory::class, function ($app) {
            return new RequestAdditionalFactory($app->make(ThrownFactory::class));
        });

        $this->app->singleton(EventDispatcherInterface::class, function ($app) {
            return $app['events'];
        });
    }

    private function registerEventListeners(): void
    {
        $events = $this->app['events'];

        if ($this->app['config']->get('effulgence.sentry.options.dsn')) {
            $listener = $this->app->make(SentryHttpListener::class);
            $events->listen(\Effulgence\Laravel\Event\HttpHandleStarted::class, [$listener, 'handleStarted']);
            $events->listen(\Effulgence\Laravel\Event\HttpHandleInterrupted::class, [$listener, 'handleInterrupted']);
        }
    }
}
