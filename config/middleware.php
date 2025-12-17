<?php

return [
    'global' => [
        // Example: App\Http\Middleware\SomeGlobalMiddleware::class,
    ],

    'route' => [
        'csrf' => App\Http\Middleware\VerifyCsrfTokenMiddleware::class,
        'maintenance' => App\Http\Middleware\MaintenanceMiddleware::class,
    ],
];