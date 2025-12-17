<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use XMVC\Container;

class ContainerTest extends TestCase
{
    public function test_it_can_bind_and_resolve_classes()
    {
        $container = new Container();
        $container->bind(ContainerTestService::class);
        
        $instance = $container->make(ContainerTestService::class);
        
        $this->assertInstanceOf(ContainerTestService::class, $instance);
    }

    public function test_it_can_bind_singletons()
    {
        $container = new Container();
        $container->singleton(ContainerTestService::class);
        
        $instance1 = $container->make(ContainerTestService::class);
        $instance2 = $container->make(ContainerTestService::class);
        
        $this->assertSame($instance1, $instance2);
    }

    public function test_it_resolves_dependencies()
    {
        $container = new Container();
        
        $instance = $container->make(ContainerTestDependentService::class);
        
        $this->assertInstanceOf(ContainerTestDependentService::class, $instance);
        $this->assertInstanceOf(ContainerTestService::class, $instance->service);
    }
}

class ContainerTestService {}

class ContainerTestDependentService {
    public $service;
    public function __construct(ContainerTestService $service)
    {
        $this->service = $service;
    }
}
