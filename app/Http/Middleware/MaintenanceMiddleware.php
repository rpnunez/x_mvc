<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use XMVC\Service\Config;

class MaintenanceMiddleware
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(Request $request, \Closure $next)
    {
        if ($this->config->get('app.maintenance') === true) {
            return new Response("503 Service Unavailable", 503);
        }

        return $next($request);
    }
}