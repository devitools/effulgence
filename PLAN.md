# Implementation Plan — Effulgence

> Effulgence: the Laravel counterpart of Serendipity.
> Delivers the same DDD capabilities, validation, serialization, and infrastructure,
> built natively on the Laravel ecosystem.

---

## Overview

Serendipity has ~100 files organized across 7 layers:

| Layer | Files | Nature |
|-------|-------|--------|
| Domain | ~20 | Framework-agnostic — can be mirrored almost 1:1 |
| Infrastructure | ~30 | Mostly framework-agnostic, ~5 with Hyperf coupling |
| Presentation | ~50 | Input depends on Hyperf FormRequest; Output is agnostic |
| Hyperf/ | ~35 | 100% Hyperf — must be entirely rewritten for Laravel |
| Testing | ~15 | Trait-based — needs adaptation for Laravel TestCase |
| Example | ~15 | Reference — recreate with Laravel idioms |
| Runtime | 1 | Global helpers (coroutine/dispatch) — simplify for Laravel |

---

## Phase 1 — Project Scaffolding

**Goal:** Base Laravel package structure with autoload, dependencies, and tooling.

### 1.1 `composer.json`
```
Namespace: Effulgence\
Autoload: PSR-4 src/ → Effulgence\
Autoload-dev: PSR-4 tests/ → Effulgence\Test\
```

**Production dependencies:**
- `php: ^8.3`
- `devitools/constructo: ^1.5.12` (shared with Serendipity)
- `laravel/framework: ^11.0|^12.0`
- `fakerphp/faker: ^1.24`
- `guzzlehttp/guzzle: ^7.9`
- `mongodb/mongodb: ^1.19`
- `monolog/monolog: ^3.8`
- `mustache/mustache: ^2.14`
- `sentry/sentry-laravel: ^4.0` (Laravel version of Sentry)
- `google/cloud-logging: ^1.32`
- `rakibtg/sleekdb: ^2.15`
- `visus/cuid2: *`

**Development dependencies:**
- `phpstan/phpstan: ^2`
- `phpunit/phpunit: ^10.5|^11.0`
- `vimeo/psalm: ^5.26`
- `friendsofphp/php-cs-fixer: ^3.0`
- `phpmd/phpmd: ^2.15`
- `rector/rector: ^2`
- `squizlabs/php_codesniffer: ^3.11`
- `orchestra/testbench: ^9.0|^10.0` (for testing Laravel packages)

**Composer scripts:**
- `test` → `./vendor/bin/phpunit`
- `lint:phpcs`, `lint:phpstan`, `lint:phpmd`, `lint:rector`, `lint:psalm`
- `lint` → runs all linters
- `fix` → rector + php-cs-fixer
- `ci` → lint + test

### 1.2 Directory structure
```
effulgence/
├── src/
│   ├── Domain/
│   ├── Infrastructure/
│   ├── Presentation/
│   ├── Laravel/
│   ├── Testing/
│   ├── _/
│   │   └── runtime.php
│   └── EffulgenceServiceProvider.php
├── config/
│   └── effulgence.php
├── tests/
├── composer.json
├── phpstan.neon
├── phpunit.xml
├── rector.php
├── psalm.xml
├── CLAUDE.md
└── README.md
```

### 1.3 `EffulgenceServiceProvider`
Equivalent of Serendipity's `ConfigProvider`:
- Registers bindings in the container (TypesFactory, SpecsFactory, factories)
- Publishes config `effulgence.php`
- Registers artisan commands
- Registers event listeners

### 1.4 `config/effulgence.php`
Consolidates configuration keys equivalent to Hyperf's:
- `schema.types` — custom types for Constructo
- `schema.specs` — reflection specs
- `exceptions.classification` — exception class → ThrowableType map
- `databases.mongo` — URI and database name
- `databases.sleek` — path and configuration
- `task.default` — correlation_id and invoker_id locations
- `cors` — allow_origin
- `logger` — format, levels
- `sentry` — dsn, options
- `http.result` — Output class → HTTP status code map

### 1.5 `runtime.php`
Simplified version for Laravel (no Swoole coroutines):
- `dispatch(object $event): void` → Laravel's `event($event)`
- `invoke(callable, ...$args): mixed` → simple call
- Remove `coroutine()` (Swoole-specific)

### 1.6 Quality tooling
- `phpstan.neon` — level 10, paths: src/ and config/
- `phpunit.xml` — test suite, coverage, extensions
- `rector.php` — refactoring rules
- `psalm.xml` — type checking

---

## Phase 2 — Domain Layer

**Goal:** Mirror Serendipity's domain layer. This layer is 100% framework-agnostic.

### 2.1 Domain/Collection/
- `Collection<T>` — wrapper over `Constructo\Type\Collection` (deprecated, keep for compatibility)

### 2.2 Domain/Contract/Adapter/
- `Serializer<T>` — interface: `serialize(array $datum): T`
- `SerializerFactory` — interface: `make(string $type): Serializer`
- `Deserializer<T>` — interface: `deserialize(mixed $instance): array`
- `DeserializerFactory` — interface: `make(string $type): Deserializer`

### 2.3 Domain/Contract/Support/
- `ThrownFactory` — interface: `make(Throwable, DateTimeImmutable): Thrown`

### 2.4 Domain/Contract/Testing/
- `Faker` — interface: `generate(string, array): mixed` and `fake(string, array): Set`
- `Helper` — interface: `truncate(string)`, `seed(string, string, array): Set`, `count(string, array): int`

### 2.5 Domain/Event/
- `RequestExecutedEvent` — readonly: method, uri, options, ?message
- `ValidationFailedEvent` — readonly: resource, values, message

### 2.6 Domain/Exception/
- `ManagedException` — ID/timestamp generation error
- `InvalidInputException` — validation with errors array
- `Misconfiguration` — configuration errors
- `RepositoryException` — data access errors with context and ThrowableType
- `UniqueKeyViolationException` — unique constraint violation
- `ThrowableType` — backed enum: INVALID_INPUT, RETRY_AVAILABLE, FALLBACK_REQUIRED, UNRECOVERABLE, UNTREATED

### 2.7 Domain/Exception/Parser/
- `Thrown` — value object with factory method `createFrom()`, `resume()`, `context()`
- `DefaultThrownFactory` — factory with configurable classification
- `Additional` — readonly value object with HTTP exception context

### 2.8 Domain/Support/
- `Truncate` — enum: BOTH, BEFORE, AFTER, NONE
- `Task` — fluent value object: resource, correlationId, invokerId

**Note:** These files can be virtually identical to Serendipity, only changing the namespace from `Serendipity\` to `Effulgence\`.

---

## Phase 3 — Infrastructure Layer

**Goal:** Implement the infrastructure layer. Most of it is framework-agnostic; items with Hyperf dependency are adapted.

### 3.1 Infrastructure/Adapter/ (framework-agnostic — mirror)
- `Serialize/Builder` — extends Constructo Builder
- `Deserialize/Demolisher` — extends Constructo Demolisher
- `Serializer<T>` — implements Domain Serializer, uses Builder
- `SerializerFactory` — factory with extensible converters
- `Deserializer<T>` — implements Domain Deserializer, uses Demolisher
- `DeserializerFactory` — factory with extensible formatters

### 3.2 Infrastructure/Database/ (framework-agnostic — mirror)
- `Managed` — ID generation (CUID2) and timestamps
- `Relational/Connection` — interface: beginTransaction, commit, rollback, insert, execute, query, fetch, run
- `Relational/ConnectionChecker` — interface: check, isAvailable
- `Relational/ConnectionFactory` — interface: make(string): Connection
- `Document/MongoFactory` — interface: make(string): MongoDB\Collection
- `Document/SleekDBFactory` — interface: make(string): SleekDB\Store
- `Document/Mongo/Condition` — interface + implementations (Between, Equal, In, Regex)
- `Document/Mongo/ConditionParser` — filter parser
- `Document/Mongo/SearchEngine` — abstract base
- `Document/Mongo/Search` — concrete query builder

### 3.3 Infrastructure/Http/ (partially agnostic)
- `ResponseType` — enum: SUCCESS, ERROR, FAIL (agnostic — mirror)
- `JsonFormatter` — implements Constructo Formatter (agnostic — mirror)
- `Received` — readonly implements Message (agnostic — mirror)
- `ExceptionResponseNormalizer` — **ADAPT**: replace `Hyperf\Validation\ValidationException` with `Illuminate\Validation\ValidationException`
- `RequestAdditionalFactory` — **ADAPT**: replace `Hyperf\HttpServer\Contract\RequestInterface` with `Illuminate\Http\Request`

### 3.4 Infrastructure/File/
- `RulesGenerator` — **ADAPT**: replace Hyperf `BASE_PATH` with Laravel `base_path()`, replace Hyperf `data_get` with Laravel `data_get`

### 3.5 Infrastructure/Logging/ (partially agnostic)
- `AbstractLogger` — PSR-3 base with Mustache (agnostic — mirror)
- `StdoutLogger` — uses Symfony Console (agnostic — mirror)
- `GoogleCloudLogger` — **ADAPT**: replace `Serendipity\Runtime\coroutine` with synchronous execution or Laravel Queue dispatch

### 3.6 Infrastructure/Repository/ (mostly agnostic)
- `Repository<T>` — base with entity() and collection() (agnostic — mirror)
- `PostgresRepository<T>` — base with bindings, columns, wildcards (agnostic — mirror)
- `MongoRepository<T>` — base with BSON transform (agnostic — mirror)
- `SleekDBRepository<T>` — minimal base (agnostic — mirror)
- `HttpRepository` — **ADAPT**: replace `Hyperf\Guzzle\ClientFactory` with `GuzzleHttp\Client` directly or Laravel HTTP client; replace `Hyperf\Support\make` with `app()`

### 3.7 Infrastructure/Repository/Adapter/ (agnostic — mirror)
- `RelationalDeserializerFactory` — formatters for relational
- `RelationalSerializerFactory` — converters for relational
- `MongoDeserializerFactory` — formatters for Mongo
- `MongoSerializerFactory` — converters for Mongo

### 3.8 Infrastructure/Repository/Formatter/ (agnostic — mirror)
- Relational: ArrayToJson, JsonToArray, DatetimeToString, TimestampToString
- Mongo: ArrayToEntity, DateTimeToDatabase, DateTimeToEntity, TimestampToDatabase, TimestampToEntity

---

## Phase 4 — Laravel Layer (replaces Hyperf/)

**Goal:** Create the entire Laravel-specific layer. This is the essence of Effulgence — every component from `Hyperf/` gets a Laravel equivalent.

### 4.1 Laravel/Command/
- `GenerateRules` — extends `Illuminate\Console\Command`
  - Signature: `effulgence:rules {source}`
  - Uses `RulesGenerator` from the Laravel container
  - Equivalent of `Hyperf\Command\GenerateRules`

### 4.2 Laravel/Database/Document/
- `LaravelMongoFactory` — implements `MongoFactory`
  - Reads config via `config('effulgence.databases.mongo')`
  - Creates `MongoDB\Client` and returns Collection
- `LaravelSleekDBFactory` — implements `SleekDBFactory`
  - Reads config via `config('effulgence.databases.sleek')`

### 4.3 Laravel/Database/Relational/
- `LaravelConnection` — implements `Connection`
  - Wraps `Illuminate\Database\Connection` (via `DB::connection()`)
  - Maps: beginTransaction, commit, rollback, insert, select (→ query), selectOne (→ fetch), statement (→ execute)
  - `run(Closure)` → accesses raw PDO via `$connection->getPdo()`
- `LaravelConnectionChecker` — implements `ConnectionChecker`
  - Uses `DB::connection()->getPdo()` to test availability
  - Retry with backoff and optional logging
- `LaravelConnectionFactory` — implements `ConnectionFactory`
  - `make(string $connection)` → `new LaravelConnection(DB::connection($connection))`

### 4.4 Laravel/Database/Relational/Support/
- `HasPostgresUniqueConstraint` — trait (mirror from Serendipity, it is agnostic)

### 4.5 Laravel/Event/
- `HttpHandleStarted` — readonly: request (Illuminate\Http\Request)
- `HttpHandleInterrupted` — readonly: request, exception
- `HttpHandleCompleted` — readonly: request, response

### 4.6 Laravel/Exception/
- `GeneralExceptionHandler` — integrates with Laravel's `App\Exceptions\Handler`
  - Can be implemented as a **reportable/renderable** via `$this->renderable()` in the Handler
  - Or as middleware that catches exceptions
  - Uses `ExceptionResponseNormalizer`, `JsonFormatter`, `RequestAdditionalFactory`
  - Maps ThrowableType → log level → JSON response
- `ValidationExceptionHandler` — handles `Illuminate\Validation\ValidationException` and `InvalidInputException`
  - Returns 400 with ResponseType::FAIL
  - Stops propagation

**Laravel-idiomatic alternative:** Instead of separate exception handlers, use the Handler's `register()` method:
```php
$this->renderable(function (ValidationException $e, Request $request) { ... });
$this->renderable(function (Throwable $e, Request $request) { ... });
```

### 4.7 Laravel/Listener/
- `SentryHttpListener` — integrates with `sentry-laravel`
  - Listens to `HttpHandleStarted`, `HttpHandleInterrupted`
  - Configures scope and captures exceptions
  - Lazy initialization based on config `effulgence.sentry.dsn`
- Remove `ResumeExitCoordinatorListener` (Hyperf-specific, no equivalent needed in Laravel)

### 4.8 Laravel/Logging/
- `GoogleCloudLoggerFactory` — factory for GCP logger
  - Reads config via `config('effulgence.logger.gcloud')`
  - Returns configured `GoogleCloudLogger`
- `StdoutLoggerFactory` — factory for console logger
  - Reads config via `config('effulgence.logger.default')`
  - Returns configured `StdoutLogger`
- **Laravel Logging integration:** Register as custom channel driver in `config/logging.php`:
  ```php
  'channels' => [
      'gcloud' => ['driver' => 'custom', 'via' => GoogleCloudLoggerFactory::class],
  ]
  ```

### 4.9 Laravel/Middleware/
- `ConnectionCheckerMiddleware` — implements Laravel Middleware
  - `handle(Request $request, Closure $next)`
  - Verifies database availability before processing
  - Reads config: `effulgence.databases.default.check.max_attempts`, `delay_microseconds`
- `CorsMiddleware` — implements Laravel Middleware
  - Note: Laravel 11+ has built-in CORS via `config/cors.php`. Evaluate if needed or if the built-in is sufficient.
  - If kept: reads config `effulgence.cors.allow_origin`
- `HttpHandlerMiddleware` — implements Laravel Middleware
  - Core of the HTTP pipeline
  - Dispatches HttpHandleStarted/Completed/Interrupted events
  - Converts `Message` and `Exportable` into `JsonResponse`
  - Detects status code via config `effulgence.http.result.{OutputClass}.status`
  - Converts Message properties to `X-{Property}` headers
  - Handles 204 No Content
- `TaskMiddleware` — implements Laravel Middleware
  - Extracts correlation_id and invoker_id from headers
  - Populates `Task` service
  - Configurable via `effulgence.task`

### 4.10 Laravel/Request/
- `LaravelFormRequest` — extends `Illuminate\Foundation\Http\FormRequest`
  - Equivalent of `HyperfFormRequest`
  - Implements `Constructo\Contract\Message`
  - Methods: `properties(): Set`, `values(): Set`, `value(string $key): mixed`
  - Overrides `failedValidation()` to dispatch `ValidationFailedEvent`
  - `normalizeHeaders()` to convert array headers

### 4.11 Laravel/Support/
- `LaravelSpecsFactory` — extends `DefaultSpecsFactory`
  - Reads `config('effulgence.schema.specs')`
- `LaravelThrownFactory` — extends `DefaultThrownFactory`
  - Reads `config('effulgence.exceptions.classification')`
- `LaravelTypesFactory` — extends `DefaultTypesFactory`
  - Reads `config('effulgence.schema.types')`

### 4.12 Laravel/Testing/
- `MongoHelper` — extends `AbstractHelper`
  - Uses `MongoFactory` from the Laravel container
  - truncate, seed, count for MongoDB
- `PostgresHelper` — extends `AbstractHelper`
  - Uses `LaravelConnectionFactory`
  - truncate, seed, count for PostgreSQL
- `SleekDBHelper` — extends `AbstractHelper`
  - Uses `SleekDBFactory` from the Laravel container

**Testing Extensions (Traits):**
- `InputExtension` — trait for testing inputs
  - Creates request context via `Request::create()`
  - Resolves Input from the container with `app()->make()`
- `LoggerExtension` — trait for asserting logs
  - `assertLogged(?pattern, ?level)` with InMemoryLogger
- `MakeExtension` — simplified trait: `make(class, args)` → `app()->make()`
- `ResourceExtension` — mirror from Testing/ (agnostic)

**Observability:**
- `InMemoryLogger` — PSR-3 in-memory logger for tests
- `Memory` — static storage (use `Illuminate\Support\Collection` instead of `Hyperf\Collection\Collection`)
- `Record` — log record value object

---

## Phase 5 — Presentation Layer

**Goal:** Adapt Input for Laravel FormRequest; Output is virtually framework-agnostic.

### 5.1 Presentation/Input.php
- Extends `Effulgence\Laravel\Request\LaravelFormRequest`
- Implements `Constructo\Contract\Message`
- Constructor: container, properties, values, rules, mappings, authorize
- Method `content(): Set` → validated values
- Method `validationData()` → processes Resolver chain (Mapped → Params)
- Adapt from Hyperf to Laravel:
  - `$this->route($key)` → works the same in Laravel
  - `data_get/data_set` → native Laravel helper functions

### 5.2 Presentation/ReflectorInput.php
- Extends `Input`
- Uses `ReflectorFactory` and `SchemaFactory` to generate rules via reflection
- Hook `using(Schema)` for schema customization
- Virtually identical, only namespace changes

### 5.3 Presentation/Input/Resolver.php
- Chain of Responsibility (agnostic — mirror)
- `then(Resolver)`, `resolve(array): array`

### 5.4 Presentation/Input/Mapped.php
- Resolver for field mappings
- **ADAPT**: replace `Hyperf\Collection\data_get/data_set` with Laravel's `data_get/data_set` (same API!)

### 5.5 Presentation/Input/Params.php
- Resolver for route parameters
- Uses `Input::route()` — works the same in Laravel

### 5.6 Presentation/Output.php + all subtypes
- **100% framework-agnostic** — mirror entirely
- `Output` — base: content + properties
- `Success` — abstract for 2xx
- `Fail\Fail` — abstract for 4xx
- `Error\Error` — abstract for 5xx
- All ~48 subtypes (Ok, Created, Accepted, NoContent, BadRequest, NotFound, InternalServerError, etc.)

---

## Phase 6 — Testing Infrastructure

**Goal:** Create the Effulgence test foundation using Orchestra Testbench.

### 6.1 Testing/FailException.php
- Mirror (agnostic)

### 6.2 Testing/Extension/ResourceExtension.php
- Mirror (agnostic)

### 6.3 Testing/Mock/ResourceExtensionMock.php
- Mirror (agnostic)

### 6.4 Testing/Resource/AbstractHelper.php
- Mirror (agnostic)

### 6.5 tests/TestCase.php
- Extends `Orchestra\Testbench\TestCase`
- Registers `EffulgenceServiceProvider`
- Defines environment config for tests
- Replaces Serendipity's `ExtensibleCase`

---

## Phase 7 — Example Implementations

**Goal:** Create examples equivalent to Serendipity's Health and Game, demonstrating patterns with Laravel.

### 7.1 Example/Health/
- `HealthInput` — extends Input with rules `message: optional|string`
- `HealthAction` — `__invoke(HealthInput): string`, logs at all levels

### 7.2 Example/Game/
**Domain (mirror — agnostic):**
- `Game`, `GameCommand`, `Feature` — entities
- `GameCollection`, `FeatureCollection` — collections
- `GameQueryRepository`, `GameCommandRepository` — interfaces

**Presentation:**
- `CreateGameInput`, `ReadGameInput`, `SearchGamesInput` — inputs adapted for Laravel
- `CreateGameAction`, `ReadGameAction`, `SearchGamesAction` — actions

**Infrastructure:**
- `PostgresGameQueryRepository`, `PostgresGameCommandRepository`
- `MongoGameQueryRepository`, `MongoGameCommandRepository`
- `SleekDBGameQueryRepository`, `SleekDBGameCommandRepository`

### 7.3 Example tests
- `HealthActionTest`
- `CreateGameActionTest`, `ReadGameActionTest`, `SearchGamesActionTest`
- `PostgresGameQueryRepositoryTest`, `PostgresGameCommandRepositoryTest`
- `MongoGameQueryRepositoryTest`
- `SleekDBGameQueryRepositoryTest`, `SleekDBGameCommandRepositoryTest`

---

## Phase 8 — Documentation and Polish

### 8.1 README.md
- Project description
- Composer installation
- ServiceProvider configuration
- Quick start with Health example
- Links to Serendipity

### 8.2 CLAUDE.md
- Update with actual project structure
- Document commands, scripts, patterns

### 8.3 CONTRIBUTING.md
- Development setup
- Code standards
- PR process

---

## Recommended Execution Order

| Step | Phase | Description | Depends on |
|------|-------|-------------|------------|
| 1 | 1.1–1.2 | composer.json + directory structure | — |
| 2 | 1.5–1.6 | runtime.php + tooling (phpstan, phpunit, etc.) | 1 |
| 3 | 2.* | Entire domain layer | 1 |
| 4 | 3.1–3.2, 3.6–3.8 | Agnostic infrastructure (Adapter, Database interfaces, Repository, Formatters) | 3 |
| 5 | 1.3 | EffulgenceServiceProvider (basic) | 1 |
| 6 | 4.3 | Laravel/Database/Relational (Connection, Factory, Checker) | 4, 5 |
| 7 | 4.2 | Laravel/Database/Document (MongoFactory, SleekDBFactory) | 4, 5 |
| 8 | 4.11 | Laravel/Support (SpecsFactory, ThrownFactory, TypesFactory) | 3, 5 |
| 9 | 3.3–3.5 | Infrastructure Http + File + Logging (adapt Hyperf parts) | 3, 8 |
| 10 | 4.10 | Laravel/Request/LaravelFormRequest | 5, 3 |
| 11 | 5.* | Presentation layer (Input, ReflectorInput, Resolvers, Output) | 10 |
| 12 | 4.9 | Laravel/Middleware (Http, Task, ConnectionChecker, Cors) | 6, 9, 11 |
| 13 | 4.5–4.6 | Laravel/Event + Exception handlers | 9, 11 |
| 14 | 4.7–4.8 | Laravel/Listener + Logging factories | 9, 13 |
| 15 | 4.1 | Laravel/Command/GenerateRules | 9 |
| 16 | 1.3 | EffulgenceServiceProvider (complete with all bindings) | 6–15 |
| 17 | 1.4 | config/effulgence.php (complete) | 16 |
| 18 | 6.* | Testing infrastructure | 4, 6, 7 |
| 19 | 4.12 | Laravel/Testing helpers (Mongo, Postgres, SleekDB) | 18 |
| 20 | 7.1 | Example/Health | 11, 18 |
| 21 | 7.2 | Example/Game (Domain → Infrastructure → Presentation) | 11, 18 |
| 22 | 7.3 | Example tests | 19, 20, 21 |
| 23 | 8.* | Documentation | 22 |

---

## Hyperf → Laravel Mapping Summary

| Serendipity (Hyperf) | Effulgence (Laravel) |
|---|---|
| `ConfigProvider::__invoke()` | `EffulgenceServiceProvider::register()` + `boot()` |
| `Hyperf\Contract\ConfigInterface` | `config()` helper or `Illuminate\Config\Repository` |
| `Hyperf\DB\DB` | `Illuminate\Support\Facades\DB` |
| `Hyperf\Validation\Request\FormRequest` | `Illuminate\Foundation\Http\FormRequest` |
| `Hyperf\Validation\ValidationException` | `Illuminate\Validation\ValidationException` |
| `Hyperf\HttpServer\CoreMiddleware` | Laravel Middleware (`handle(Request, Closure)`) |
| `Hyperf\ExceptionHandler\ExceptionHandler` | `App\Exceptions\Handler` → `renderable()` |
| `Hyperf\Command\Command` | `Illuminate\Console\Command` |
| `Hyperf\Event\Contract\ListenerInterface` | Laravel Event Listener classes |
| `Hyperf\Event\Annotation\Listener` | `EventServiceProvider::$listen` or `Event::listen()` |
| `Hyperf\Context\Context` | `request()` helper / middleware binding |
| `Hyperf\HttpMessage\Stream\SwooleStream` | `Illuminate\Http\JsonResponse` |
| `Hyperf\Guzzle\ClientFactory` | `Illuminate\Http\Client\Factory` or Guzzle directly |
| `Hyperf\Collection\data_get/data_set` | `data_get/data_set` (native Laravel) |
| `Hyperf\Support\make()` | `app()->make()` or `resolve()` |
| `Hyperf\Coordinator` | Not needed (Laravel does not use coroutines) |
| `Swow\Psr7\Message\ResponsePlusInterface` | `Illuminate\Http\Response` / `JsonResponse` |
| `Hyperf\Collection\Collection` | `Illuminate\Support\Collection` |
