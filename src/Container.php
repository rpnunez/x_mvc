<?php

namespace XMVC;

use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * Service Container class for dependency injection and management.
 */
class Container
{
    /**
     * The current globally available container (if any).
     *
     * @var static|null
     */
    protected static $instance;

    /**
     * The container's bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The container's shared instances.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Retrieves the single instance of the class. Creates a new instance if it does not already exist.
     * @return static The single instance of the class.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     *
     * @param  Container  $container
     * @return void
     */
    public static function setInstance(Container $container)
    {
        static::$instance = $container;
    }

    /**
     * Binds an abstract type to a concrete implementation. If the concrete implementation is not provided, the abstract type itself is used as the concrete.
     *
     * @param string     $abstract The abstract type to bind.
     * @param mixed|null $concrete The concrete implementation to bind to the abstract type. Defaults to null.
     *
     * @return void
     */
    public function bind($abstract, $concrete = null)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Marks a given abstract type as a singleton by binding it to a concrete implementation.
     * The singleton instance is initialized as null, to be resolved later.
     *
     * @param string      $abstract The abstract type to be bound as a singleton.
     * @param string|null $concrete The concrete implementation to bind to the abstract type, or null if it should be resolved automatically.
     *
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete);
        $this->instances[$abstract] = null; // Mark as singleton
    }

    /**
     * Resolves and returns an instance of the given abstract type from the container.
     * Handles singleton instances and creates new ones as necessary.
     *
     * @param string $abstract The abstract type or alias to be resolved from the container.
     * @param array  $parameters Parameters to override dependencies (associative array).
     *
     * @return mixed The resolved instance of the requested type.
     */
    public function make($abstract, array $parameters = [])
    {
        // Return existing singleton instance if available
        if (array_key_exists($abstract, $this->instances) && !is_null($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract] ?? $abstract;

        if ($concrete === $abstract || $concrete instanceof \Closure) {
             $object = $this->build($concrete, $parameters);
        } else {
             $object = $this->make($concrete, $parameters);
        }

        // Store singleton instance if marked
        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Builds and resolves an instance of the given concrete class. If the class has dependencies,
     * they are resolved recursively.
     *
     * @param mixed $concrete The class name or a closure representing the object to instantiate.
     * @param array $parameters Parameters to override dependencies.
     *
     * @return mixed The instantiated object.
     * @throws Exception If the target class does not exist, is not instantiable, or dependencies cannot be resolved.
     */
    protected function build($concrete, array $parameters = [])
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new Exception("Target class [$concrete] does not exist.", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
             throw new Exception("Target [$concrete] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $instances = $this->resolveDependencies($dependencies, $parameters);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Resolve all the dependencies from the ReflectionParameters.
     *
     * @param  \ReflectionParameter[]  $dependencies
     * @param  array  $parameters
     * @return array
     *
     * @throws Exception
     */
    protected function resolveDependencies($dependencies, array $parameters = [])
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            // If the dependency name is in the parameters, use that value
            if (array_key_exists($dependency->getName(), $parameters)) {
                $results[] = $parameters[$dependency->getName()];
                continue;
            }

            $type = $dependency->getType();

            if (!$type || $type->isBuiltin()) {
                if ($dependency->isDefaultValueAvailable()) {
                    $results[] = $dependency->getDefaultValue();
                } else {
                    throw new Exception("Unresolvable dependency resolving [$dependency] in class {$dependency->getDeclaringClass()->getName()}");
                }
            } else {
                $results[] = $this->make($type->getName());
            }
        }

        return $results;
    }

    /**
     * Dynamically retrieve methods from the container.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return static::getInstance()->$method(...$parameters);
    }

    /**
     * Call a static method on the container instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$parameters);
        }

        if (isset($this->bindings[$method])) {
            return $this->make($method);
        }
        throw new Exception("Method [$method] not found.");
    }

    /**
     * Flush the container of all bindings and resolved instances.
     *
     * @return void
     */
    public function flush()
    {
        $this->bindings = [];
        $this->instances = [];
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]);
    }

    /**
     * Determine if the given abstract type has been resolved.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function resolved($abstract)
    {
        return isset($this->instances[$abstract]) && !is_null($this->instances[$abstract]);
    }

    /**
     * Remove a binding from the container.
     *
     * @param  string  $abstract
     * @return void
     */
    public function forget($abstract)
    {
        unset($this->bindings[$abstract], $this->instances[$abstract]);
    }

    /**
     * Remove all the extender callbacks for a given type.
     *
     * @param  string  $abstract
     * @return void
     */
    public function forgetExtenders($abstract)
    {
        unset($this->extenders[$abstract]);
    }
}
