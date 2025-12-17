<?php

namespace XMVC\Event;

class CsrfTokenGenerated
{
    /**
     * The generated token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new event instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }
}