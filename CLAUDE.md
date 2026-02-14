# CLAUDE.md — AI Assistant Guide for Effulgence

This file provides context and conventions for AI assistants (e.g., Claude) working in this repository.

## Project Overview

**Effulgence** is a PHP library under the **devitools** organization that extends the **Laravel** framework with advanced Domain-Driven Design (DDD) capabilities, intelligent validation, automatic serialization, and robust infrastructure.

Effulgence is the Laravel counterpart of [**Serendipity**](https://github.com/devitools/serendipity), which does the same for Hyperf. Both share the same philosophy and architectural vision, but each is built natively for its respective framework ecosystem.

### Relationship with Serendipity

| | Serendipity | Effulgence |
|---|---|---|
| **Framework** | Hyperf (Swoole/async) | Laravel |
| **Purpose** | DDD + infrastructure for Hyperf | DDD + infrastructure for Laravel |
| **Foundation** | Constructo (metaprogramming) | Constructo (metaprogramming) |
| **PHP Version** | 8.3+ | 8.3+ |

Both projects share:
- The same DDD architecture (Domain, Infrastructure, Presentation layers)
- Constructo as the metaprogramming foundation for dependency resolution and data formatting
- Similar patterns for entities, validation, serialization, repositories, and collections
- The same quality standards (PHPStan, Psalm, PHPUnit, etc.)

The key difference is that Effulgence leverages Laravel's ecosystem (Eloquent, service providers, middleware, artisan commands, etc.) instead of Hyperf's (annotations, coroutines, config providers).

### Mapping Hyperf concepts to Laravel

| Serendipity (Hyperf) | Effulgence (Laravel) |
|---|---|
| `ConfigProvider` | `ServiceProvider` |
| Hyperf annotations/DI | Laravel service container / providers |
| Hyperf middleware | Laravel middleware |
| Hyperf commands | Artisan commands |
| Hyperf events/listeners | Laravel events/listeners |
| Hyperf validation | Laravel Form Requests / Validation |
| Hyperf coroutine context | Laravel request lifecycle |

## Repository Status

The core implementation is complete with **119 source files** and a comprehensive test suite with **332 tests** and **878 assertions**. The project covers all major DDD layers and Laravel integrations.

## Directory Structure

```
effulgence/
├── src/
│   ├── Domain/                          # Pure business logic (framework-agnostic)
│   │   ├── Collection/                  # Type-safe collections (Collection)
│   │   ├── Contract/                    # Interfaces
│   │   │   ├── Adapter/                 # Serializer/Deserializer contracts
│   │   │   ├── Support/                 # ThrownFactory contract
│   │   │   └── Testing/                 # Faker, Helper contracts
│   │   ├── Event/                       # Domain events (RequestExecuted, ValidationFailed)
│   │   ├── Exception/                   # Domain exceptions and parsing
│   │   │   └── Parser/                  # Thrown, Additional, DefaultThrownFactory
│   │   └── Support/                     # Domain utilities (Task, Truncate)
│   ├── Infrastructure/                  # Technical implementations
│   │   ├── Adapter/                     # Serialize/Deserialize via Constructo
│   │   │   ├── Deserialize/             # Demolisher
│   │   │   └── Serialize/               # Builder
│   │   ├── Database/                    # Database concerns
│   │   │   ├── Document/                # MongoDB/SleekDB (conditions, search, factories)
│   │   │   │   └── Mongo/              # Condition parser, search engine
│   │   │   │       └── Condition/      # Between, Equal, In, Regex conditions
│   │   │   └── Relational/             # Connection, ConnectionChecker, ConnectionFactory
│   │   ├── File/                        # File handling (RulesGenerator)
│   │   ├── Http/                        # HTTP concerns (JsonFormatter, ExceptionResponseNormalizer)
│   │   ├── Logging/                     # StdoutLogger, GoogleCloudLogger, AbstractLogger
│   │   └── Repository/                  # Repository implementations
│   │       ├── Adapter/                 # Mongo/Relational serializer factories
│   │       ├── Formatter/               # Type-specific formatters (datetime, json, arrays)
│   │       ├── HttpRepository.php       # Guzzle-based HTTP repository
│   │       ├── MongoRepository.php      # MongoDB repository
│   │       ├── PostgresRepository.php   # PostgreSQL repository
│   │       ├── Repository.php           # Base repository
│   │       └── SleekDBRepository.php    # SleekDB repository
│   ├── Presentation/                    # Input/Output handling
│   │   ├── Input/                       # Mapped, Params, Resolver
│   │   ├── Output/                      # JSend-style response classes
│   │   │   ├── Error/                   # 5xx error responses (10 classes)
│   │   │   └── Fail/                    # 4xx fail responses (25+ classes)
│   │   ├── Input.php                    # Base input (validation rules)
│   │   ├── Output.php                   # Base output
│   │   └── ReflectorInput.php           # Reflection-based input
│   ├── Laravel/                         # Laravel-specific bindings
│   │   ├── Command/                     # Artisan commands (GenerateRules)
│   │   ├── Database/                    # Laravel DB implementations
│   │   │   ├── Document/               # LaravelMongoFactory, LaravelSleekDBFactory
│   │   │   └── Relational/             # LaravelConnection, Checker, Factory
│   │   │       └── Support/            # HasPostgresUniqueConstraint
│   │   ├── Event/                       # HTTP handle events
│   │   ├── Exception/                   # Exception handlers (General, Validation)
│   │   ├── Listener/                    # SentryHttpListener
│   │   ├── Logging/                     # Logger factories (Stdout, GoogleCloud)
│   │   ├── Middleware/                  # CORS, ConnectionChecker, Task, HttpHandler
│   │   ├── Request/                     # LaravelFormRequest
│   │   ├── Support/                     # Specs, Thrown, Types factories
│   │   └── Testing/                     # Test extensions and mocks
│   │       ├── Extension/               # Input, Logger, Make extensions
│   │       ├── Mock/                    # Extension mocks
│   │       └── Observability/           # InMemoryLogger for testing
│   ├── Testing/                         # Framework-agnostic test utilities
│   │   ├── Extension/                   # ResourceExtension
│   │   ├── Mock/                        # ResourceExtensionMock
│   │   └── Resource/                    # AbstractHelper
│   ├── _/runtime.php                    # Runtime functions (invoke, dispatch)
│   └── EffulgenceServiceProvider.php    # Laravel service provider
├── tests/
│   ├── Domain/Entity/                   # Entity tests
│   ├── Infrastructure/                  # Adapter, Database, Exception, Http, Logger, Repository tests
│   ├── Presentation/                    # Output tests (Success, Fail, Error parametrized)
│   ├── Laravel/                         # Exception, Middleware, Support, Logging, Database tests
│   ├── Testing/                         # Test stubs and fixtures
│   ├── _/                               # Runtime function tests
│   └── bootstrap.php                    # Test bootstrap
├── composer.json
├── phpstan.neon
├── phpunit.xml
├── CLAUDE.md
└── README.md
```

## Development Workflow

### Git Conventions

- **Branch naming**: Use descriptive branch names prefixed with a category (e.g., `feat/`, `fix/`, `docs/`, `refactor/`).
- **Commit messages**: Write clear, imperative-mood messages (e.g., "Add user authentication module").
- **Pull requests**: Include a summary of changes, motivation, and any testing performed.

### Build & Run

```bash
composer install          # Install dependencies
composer test             # Run tests
composer lint             # Run static analysis (PHPStan, Psalm, etc.)
composer fix              # Auto-fix code style
composer ci               # Run lint + test
```

### Testing

- **Framework**: PHPUnit 11
- **Tests**: 332 tests, 878 assertions
- **Skipped**: 3 tests require `ext-mongodb` (annotated with `#[RequiresPhpExtension('mongodb')]`)
- Run: `composer test` or `./vendor/bin/phpunit`
- Run without coverage: `./vendor/bin/phpunit --no-coverage`
- Test namespace: `Effulgence\Test\`
- Bootstrap: `tests/bootstrap.php`

### Key Dependencies

| Package | Purpose |
|---|---|
| `devitools/constructo` | Metaprogramming foundation (shared with Serendipity) |
| `laravel/framework` | Core framework (^11.0 or ^12.0) |
| `mongodb/mongodb` | MongoDB driver |
| `guzzlehttp/guzzle` | HTTP client for HttpRepository |
| `mustache/mustache` | Template engine for StdoutLogger |
| `rakibtg/sleekdb` | Flat-file NoSQL database |
| `sentry/sentry-laravel` | Error tracking |
| `phpstan/phpstan` | Static analysis |
| `phpunit/phpunit` | Testing (^10.5 or ^11.0) |
| `vimeo/psalm` | Type checking |
| `orchestra/testbench` | Laravel package testing |

## Key Conventions

### Code Style

- PHP 8.3+ with strict types (`declare(strict_types=1)`)
- Readonly classes and properties for entities
- Use PHP 8 attributes for metadata (validation rules, serialization hints, test requirements)
- PSR-4 autoloading under the `Effulgence\` namespace
- Follow the same patterns established in Serendipity

### Architecture

- **Domain layer** is framework-agnostic — no Laravel imports in `Domain/`
- **Infrastructure** contains all technical implementations
- **Presentation** handles input validation and output formatting
- **Laravel/** contains all framework-specific bindings and adapters
- Contracts (interfaces) live in `Domain/Contract/`, implementations in `Infrastructure/`
- Runtime functions (`invoke`, `dispatch`) live in `src/_/runtime.php`

### Naming

- Namespace: `Effulgence\`
- Test namespace: `Effulgence\Test\`
- Service provider: `EffulgenceServiceProvider`
- Follow Laravel conventions for artisan commands, middleware, events, etc.
- Follow Serendipity conventions for domain concepts (entities, repositories, collections)

### Testing Patterns

- Tests mirror the `src/` directory structure under `tests/`
- Test stubs and fixtures live in `tests/Testing/Stub/`
- Repository tests use concrete mock classes (e.g., `HttpRepositoryTestMock`) to expose protected methods
- Parametrized tests use PHPUnit `#[DataProvider]` for Success/Fail/Error output classes
- Tests requiring Laravel container set up `Container::getInstance()` with needed bindings in `setUp()`
- Tests requiring facades set up `Facade::setFacadeApplication()` and clean up in `tearDown()`
- MongoDB-dependent tests use `#[RequiresPhpExtension('mongodb')]` to skip gracefully

## For AI Assistants

When working in this repository:

1. **All code, comments, and text must be written in English.**
2. **Understand the Serendipity reference** — Before implementing a feature, check how Serendipity solves it for Hyperf at [github.com/devitools/serendipity](https://github.com/devitools/serendipity). Adapt the concept to Laravel idioms, don't just copy-paste.
3. **Keep the Domain layer clean** — Never import Laravel classes in `src/Domain/`. Use contracts/interfaces instead.
4. **Read before writing** — Always read existing files before modifying them.
5. **Stay focused** — Only make changes that are directly requested.
6. **Keep this file updated** — When you make significant structural changes, update this CLAUDE.md.
7. **Follow existing patterns** — Match the style and conventions already present in the codebase.
8. **Test your changes** — Run the test suite before considering work complete.
9. **Don't over-engineer** — Prefer simple, direct solutions.
