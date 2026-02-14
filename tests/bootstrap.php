<?php

declare(strict_types=1);

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

putenv('APP_ENV=test');
putenv('FAKER_LOCALE=en_US');

require_once __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
date_default_timezone_set('UTC');
