<?php

namespace App\Http;

use App\Http\Middleware\LogRequestMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\MaintenanceMiddleware;
use App\Http\Middleware\VerifyCsrfTokenMiddleware;

class Kernel
{
    protected $middleware = [
        MaintenanceMiddleware::class,
        LogRequestMiddleware::class,
        VerifyCsrfTokenMiddleware::class,
    ];

    protected $routeMiddleware = [
        'auth' => AuthMiddleware::class,
    ];
}