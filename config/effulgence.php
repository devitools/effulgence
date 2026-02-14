<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Schema Configuration
    |--------------------------------------------------------------------------
    |
    | Custom types and specs for the Constructo metaprogramming foundation.
    |
    */
    'schema' => [
        'types' => [],
        'specs' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Exception Classification
    |--------------------------------------------------------------------------
    |
    | Map exception classes to ThrowableType values for structured error handling.
    | Example: \App\Exceptions\CustomException::class => 'invalid_input'
    |
    */
    'exceptions' => [
        'classification' => [],
        'ignore' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for MongoDB, SleekDB, and connection checking.
    |
    */
    'databases' => [
        'mongo' => [
            'uri' => env('MONGO_URI', 'mongodb://localhost:27017'),
            'database' => env('MONGO_DATABASE', 'effulgence'),
        ],
        'sleek' => [
            'path' => env('SLEEKDB_PATH', storage_path('sleekdb')),
            'configuration' => [
                'auto_cache' => true,
                'cache_lifetime' => null,
                'timeout' => 120,
                'primary_key' => '_id',
            ],
        ],
        'default' => [
            'check' => [
                'max_attempts' => 3,
                'delay_microseconds' => 100,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Task Configuration
    |--------------------------------------------------------------------------
    |
    | Define where to extract correlation_id and invoker_id from requests.
    |
    */
    'task' => [
        'default' => [
            'correlation_id' => ['X-Correlation-ID', 'header'],
            'invoker_id' => ['X-Invoker-ID', 'header'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Cross-Origin Resource Sharing.
    | Note: Laravel 11+ has built-in CORS via config/cors.php.
    |
    */
    'cors' => [
        'allow_origin' => env('CORS_ALLOW_ORIGIN', '*'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logger Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the logging infrastructure.
    |
    */
    'logger' => [
        'default' => [
            'levels' => [
                'alert',
                'critical',
                'emergency',
                'error',
                'warning',
                'notice',
                'info',
                'debug',
            ],
            'format' => '[{{env}}.{{level}}] {{message}}: {{context}}',
        ],
        'gcloud' => [
            'project_id' => env('GCLOUD_PROJECT_ID', 'unknown'),
            'service_name' => env('GCLOUD_SERVICE_NAME', 'unknown'),
            'format' => '{{message}} | {{resource}} | {{correlation_id}} | {{invoker_id}}',
            'options' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sentry Configuration
    |--------------------------------------------------------------------------
    |
    | Sentry integration settings.
    |
    */
    'sentry' => [
        'dsn' => env('SENTRY_LARAVEL_DSN'),
        'debug' => env('SENTRY_DEBUG', false),
        'options' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Result Configuration
    |--------------------------------------------------------------------------
    |
    | Map Output classes to HTTP status codes.
    | Example: \Effulgence\Presentation\Output\Created::class => ['status' => 201]
    |
    */
    'http' => [
        'result' => [],
    ],
];
