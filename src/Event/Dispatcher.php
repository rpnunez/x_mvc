<?php

namespace XMVC\Event;

use XMVC\Container;

class Dispatcher
{
    /**
     * The container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * The registered listeners.
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * The registered wildcard listeners.
     *
     * @var array
     */
    protected $wildcards = [];

    /**
     * Create a new Dispatcher instance.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  string|array  $events
     * @param  mixed  $listener
     * @return void
     */
    public function listen($events, $listener)
    {
        foreach ((array) $events as $event) {
            if (strpos($event, '*') !== false) {
                $this->wildcards[$event][] = $listener;
            } else {
                $this->listeners[$event][] = $listener;
            }
        }
    }

    /**
     * Determine if a given event has listeners.
     *
     * @param  string  $eventName
     * @return bool
     */
    public function hasListeners($eventName)
    {
        if (isset($this->listeners[$eventName])) {
            return true;
        }

        foreach ($this->wildcards as $pattern => $listeners) {
            if (fnmatch($pattern, $eventName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param  object|string  $subscriber
     * @return void
     */
    public function subscribe($subscriber)
    {
        $subscriber = $this->resolveSubscriber($subscriber);

        $subscriber->subscribe($this);
    }

    /**
     * Dispatch an event and call the listeners.
     *
     * @param  object|string  $event
     * @param  mixed  $payload
     * @return mixed The passed event or payload.
     */
    public function dispatch($event, $payload = [])
    {
        $eventName = is_object($event) ? get_class($event) : $event;
        $data = is_object($event) ? $event : $payload;

        foreach ($this->getListeners($eventName) as $listener) {
            // Check if the event has been stopped
            if ($data instanceof StoppableEventInterface && $data->isPropagationStopped()) {
                break;
            }

            $this->executeListener($listener, $data);
        }

        return $data;
    }

    /**
     * Get the listeners for a specific event.
     *
     * @param  string  $eventName
     * @return array
     */
    public function getListeners($eventName)
    {
        $listeners = $this->listeners[$eventName] ?? [];

        foreach ($this->wildcards as $pattern => $wildcardListeners) {
            if (fnmatch($pattern, $eventName)) {
                $listeners = array_merge($listeners, $wildcardListeners);
            }
        }

        return $listeners;
    }

    /**
     * Execute the listener.
     *
     * @param  mixed   $listener
     * @param  object  $event
     * @return void
     */
    protected function executeListener($listener, $event)
    {
        // If the listener is a class string, resolve it from the container
        if (is_string($listener)) {
            $instance = $this->container->make($listener);
            $method = 'handle';
        } elseif (is_array($listener)) {
            $instance = $listener[0];
            $method = $listener[1];
        } else {
            // Closure or invokable object
            $instance = $listener;
            $method = '__invoke';
        }

        $instance->$method($event);
    }

    /**
     * Resolve the subscriber instance.
     *
     * @param  object|string  $subscriber
     * @return mixed
     */
    protected function resolveSubscriber($subscriber)
    {
        if (is_string($subscriber)) {
            return $this->container->make($subscriber);
        }

        return $subscriber;
    }
}