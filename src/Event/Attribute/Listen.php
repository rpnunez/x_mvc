<?php

namespace XMVC\Event\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Listen
{
    /**
     * Create a new Listen attribute instance.
     *
     * @param string $event The event class name to listen for.
     */
    public function __construct(
        public string $event
    ) {}
}