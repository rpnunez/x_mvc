<?php

namespace XMVC\Event;

interface StoppableEventInterface
{
    /**
     * Determine if the event propagation should stop.
     *
     * @return bool
     */
    public function isPropagationStopped();

    /**
     * Stop the propagation of the event to further listeners.
     */
    public function stopPropagation();
}