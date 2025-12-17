<?php

namespace XMVC\Event;

trait HasEvents
{
    /**
     * The event dispatcher instance.
     *
     * @var Dispatcher
     */
    protected static $dispatcher;

    /**
     * Set the event dispatcher instance.
     *
     * @param Dispatcher $dispatcher
     */
    public static function setEventDispatcher(Dispatcher $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

    /**
     * Register an observer with the class.
     *
     * @param  object|string  $class
     * @return void
     */
    public static function observe($class)
    {
        $instance = is_string($class) ? new $class : $class;
        $className = static::class;

        foreach (['creating', 'created', 'updating', 'updated', 'deleting', 'deleted'] as $event) {
            if (method_exists($instance, $event)) {
                static::$dispatcher->listen("eloquent.{$event}: {$className}", [$instance, $event]);
            }
        }
    }

    /**
     * Fire a model event.
     *
     * @param  string  $event
     * @param  mixed   $payload
     * @return void
     */
    protected static function fireModelEvent($event, $payload)
    {
        if (isset(static::$dispatcher)) {
            static::$dispatcher->dispatch("eloquent.{$event}: " . static::class, $payload);
        }
    }
}