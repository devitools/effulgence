# Effulgence

> The Laravel missing component — DDD capabilities, intelligent validation, automatic serialization, and robust infrastructure.

Effulgence is the Laravel counterpart of [**Serendipity**](https://github.com/devitools/serendipity). Both share the same philosophy and architectural vision, but Effulgence is built natively for the Laravel ecosystem.

## Features

- **Domain-Driven Design** — Pure business logic layer, framework-agnostic
- **Intelligent Validation** — Reflection-based input validation with Laravel Form Requests
- **Automatic Serialization** — Type-safe serialization/deserialization via [Constructo](https://github.com/devitools/constructo)
- **Repository Pattern** — Support for PostgreSQL, MongoDB, SleekDB, and HTTP
- **Presentation Layer** — Structured input/output handling with automatic HTTP responses
- **Laravel Integration** — Native service provider, middleware, commands, and events

## Installation

```bash
composer require devitools/effulgence
```

## Requirements

- PHP 8.3 or higher
- Laravel 11.0 or higher

## Project Status

This project is currently in initial development. The structure and features are being actively built based on the comprehensive plan outlined in [PLAN.md](PLAN.md).

## Architecture

Effulgence follows a layered architecture:

```
effulgence/
├── src/
│   ├── Domain/              # Pure business logic (framework-agnostic)
│   ├── Infrastructure/      # Technical implementations
│   ├── Presentation/        # Input/Output handling
│   ├── Laravel/             # Laravel-specific bindings
│   └── Testing/             # Test utilities
```

See [PLAN.md](PLAN.md) for the complete implementation plan.

## Relationship with Serendipity

| | Serendipity | Effulgence |
|---|---|---|
| **Framework** | Hyperf (Swoole/async) | Laravel |
| **Purpose** | DDD + infrastructure for Hyperf | DDD + infrastructure for Laravel |
| **Foundation** | Constructo (metaprogramming) | Constructo (metaprogramming) |
| **PHP Version** | 8.3+ | 8.3+ |

Both projects share the same DDD architecture, validation patterns, and quality standards.

## Development

### Setup

```bash
composer install
```

### Testing

```bash
composer test
```

### Code Quality

```bash
# Run all linters
composer lint

# Auto-fix code style
composer fix

# Run CI pipeline (lint + test)
composer ci
```

## Documentation

- [PLAN.md](PLAN.md) — Comprehensive implementation plan
- [CLAUDE.md](CLAUDE.md) — AI assistant guide for working in this repository

## Contributing

Contributions are welcome! Please ensure all tests pass and code meets our quality standards before submitting a PR.

## License

MIT License - see LICENSE file for details.

## Credits

Created and maintained by [devitools](https://github.com/devitools).

Part of the devitools ecosystem alongside [Serendipity](https://github.com/devitools/serendipity) and [Constructo](https://github.com/devitools/constructo).
