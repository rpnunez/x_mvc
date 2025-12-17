<?php

namespace XMVC\Event;

use Exception;

class FakeDispatcher extends Dispatcher
{
    /**
     * The dispatched events.
     *
     * @var array
     */
    protected $dispatched = [];

    /**
     * Dispatch an event and capture it instead of executing listeners.
     *
     * @param  object  $event
     * @return object
     */
    public function dispatch($event)
    {
        $this->dispatched[get_class($event)][] = $event;

        return $event;
    }

    /**
     * Assert that an event was dispatched.
     *
     * @param  string  $event
     * @param  callable|null  $callback
     * @return void
     * @throws Exception
     */
    public function assertDispatched($event, $callback = null)
    {
        if (!isset($this->dispatched[$event])) {
            throw new Exception("The expected event [{$event}] was not dispatched.");
        }

        if ($callback) {
            foreach ($this->dispatched[$event] as $dispatchedEvent) {
                if ($callback($dispatchedEvent)) {
                    return;
                }
            }
            throw new Exception("The expected event [{$event}] was dispatched but failed the callback check.");
        }
    }

    /**
     * Assert that an event was not dispatched.
     *
     * @param  string  $event
     * @return void
     * @throws Exception
     */
    public function assertNotDispatched($event)
    {
        if (isset($this->dispatched[$event])) {
            throw new Exception("The unexpected event [{$event}] was dispatched.");
        }
    }

    /**
     * Assert that no events were dispatched.
     *
     * @return void
     * @throws Exception
     */
    public function assertNothingDispatched()
    {
        if (!empty($this->dispatched)) {
            throw new Exception("Events were dispatched unexpectedly.");
        }
    }

    /**
     * Get all dispatched events.
     *
     * @return array
     */
    public function dispatched()
    {
        return $this->dispatched;
    }
}