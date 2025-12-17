<?php

use XMVC\Container;
use XMVC\Service\Auth;
use XMVC\Service\Cache;
use XMVC\Service\Config;
use XMVC\Service\Db;
use XMVC\Service\Flash;
use XMVC\Service\Log;
use XMVC\Service\Router;
use App\Http\Request;
use XMVC\Service\Session;
use XMVC\Service\ValidatorFactory;

// Create the container
$container = new Container();
Container::setInstance($container);

// Bind core services
$container->singleton(Config::class, function () {
    return new Config();
});

$container->singleton(Log::class, function ($container) {
    return new Log($container->make(Config::class));
});

$container->singleton(Cache::class, function ($container) {
    return new Cache($container->make(Config::class));
});

$container->singleton(Db::class, function ($container) {
    return new Db($container->make(Config::class));
});

$container->singleton(Session::class, function () {
    return new Session();
});

$container->singleton(Auth::class, function ($container) {
    return new Auth($container->make(Session::class));
});

$container->singleton(Flash::class, function ($container) {
    return new Flash($container->make(Session::class));
});

$container->singleton(ValidatorFactory::class, function () {
    return new ValidatorFactory();
});

$container->singleton(Request::class, function () {
    return new Request();
});

$container->singleton(Router::class, function () {
    $router = new Router();
    require_once BASE_PATH . '/routes/web.php';
    require_once BASE_PATH . '/routes/api.php';
    return $router;
});

return $container;