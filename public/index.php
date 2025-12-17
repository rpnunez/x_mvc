<?php

use XMVC\Kernel;

// Define the absolute path to the project root
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

// Bootstrap the application
$container = require_once BASE_PATH . '/bootstrap/app.php';

// Use the container to create the application kernel
$kernel = $container->make(Kernel::class);
$kernel->handle();