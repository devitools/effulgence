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

## Serendipity Reference Architecture

Use Serendipity's structure as a reference for the capabilities Effulgence should provide, adapted to Laravel idioms:

```
serendipity/src/
├── Domain/                     # Pure business logic (framework-agnostic)
│   ├── Collection/             # Type-safe collections
│   ├── Contract/               # Interfaces (Adapter, Support, Testing)
│   │   └── Adapter/            # Serializer/Deserializer contracts
│   ├── Event/                  # Domain events
│   ├── Exception/              # Domain exceptions
│   └── Support/                # Domain utilities
├── Infrastructure/             # Technical implementations
│   ├── Adapter/                # Serialize/Deserialize implementations
│   ├── Database/               # Database concerns
│   ├── File/                   # File handling
│   ├── Http/                   # HTTP client concerns
│   ├── Logging/                # Logging infrastructure
│   └── Repository/             # Repository implementations
│       ├── Repository.php      #   Base repository
│       ├── PostgresRepository.php
│       ├── MongoRepository.php
│       ├── HttpRepository.php
│       └── SleekDBRepository.php
├── Presentation/               # External interfaces
│   ├── Input/                  # Input handling
│   ├── Output/                 # Output formatting
│   ├── Input.php               # Base input (validation rules)
│   ├── Output.php              # Base output
│   └── ReflectorInput.php      # Reflection-based input
├── Hyperf/                     # **Hyperf-specific bindings** (Effulgence replaces this with Laravel/)
│   ├── Command/
│   ├── Database/
│   ├── Event/
│   ├── Exception/
│   ├── Listener/
│   ├── Logging/
│   ├── Middleware/
│   ├── Request/
│   ├── Support/
│   └── Testing/
├── Testing/                    # Test utilities
├── Example/                    # Reference implementations
└── ConfigProvider.php          # Hyperf config (Effulgence uses ServiceProvider)
```

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

This project is in its initial stage. The structure below will evolve as development progresses.

## Expected Directory Structure

```
effulgence/
├── src/
│   ├── Domain/                 # Pure business logic (can mirror Serendipity's Domain/)
│   │   ├── Collection/
│   │   ├── Contract/
│   │   ├── Event/
│   │   ├── Exception/
│   │   └── Support/
│   ├── Infrastructure/         # Technical implementations
│   │   ├── Adapter/
│   │   ├── Database/
│   │   ├── File/
│   │   ├── Http/
│   │   ├── Logging/
│   │   └── Repository/
│   ├── Presentation/           # Input/Output handling
│   │   ├── Input/
│   │   └── Output/
│   ├── Laravel/                # Laravel-specific bindings (replaces Hyperf/)
│   │   ├── Command/
│   │   ├── Database/
│   │   ├── Event/
│   │   ├── Exception/
│   │   ├── Listener/
│   │   ├── Logging/
│   │   ├── Middleware/
│   │   ├── Request/
│   │   ├── Support/
│   │   └── Testing/
│   ├── Testing/
│   └── EffulgenceServiceProvider.php
├── config/
├── tests/
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
```

### Testing

- **Framework**: PHPUnit
- Run: `composer test` or `./vendor/bin/phpunit`
- Mirror Serendipity's quality standards: PHPStan, Psalm, PHP CS Fixer, PHPMD, Rector

### Key Dependencies

| Package | Purpose |
|---|---|
| `devitools/constructo` | Metaprogramming foundation (shared with Serendipity) |
| `laravel/framework` | Core framework |
| `phpstan/phpstan` | Static analysis |
| `phpunit/phpunit` | Testing |
| `vimeo/psalm` | Type checking |

## Key Conventions

### Code Style

- PHP 8.3+ with strict types (`declare(strict_types=1)`)
- Readonly classes and properties for entities
- Use PHP 8 attributes for metadata (validation rules, serialization hints)
- PSR-4 autoloading under the `Effulgence\` namespace
- Follow the same patterns established in Serendipity

### Architecture

- **Domain layer** is framework-agnostic — no Laravel imports in `Domain/`
- **Infrastructure** contains all technical implementations
- **Presentation** handles input validation and output formatting
- **Laravel/** contains all framework-specific bindings and adapters
- Contracts (interfaces) live in `Domain/Contract/`, implementations in `Infrastructure/`

### Naming

- Namespace: `Effulgence\`
- Service provider: `EffulgenceServiceProvider`
- Follow Laravel conventions for artisan commands, middleware, events, etc.
- Follow Serendipity conventions for domain concepts (entities, repositories, collections)

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
