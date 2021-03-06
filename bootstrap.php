<?php
use App\Utils\DotEnv;

require __DIR__ . "/vendor/autoload.php";

// Configure environment
date_default_timezone_set('UTC');

// Define constants
define('APP_NAME', 'Dp3tMicroBackend');
define('APP_VERSION', '0.0.2-alpha');

// Load root ".env" file
DotEnv::load(__DIR__ . '/.env');
DotEnv::required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);
