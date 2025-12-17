<?php

namespace XMVC\Service;

class Flash
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function success($message)
    {
        $this->session->set('flash_message', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function error($message)
    {
        $this->session->set('flash_message', [
            'type' => 'error',
            'message' => $message,
        ]);
    }

    public function get()
    {
        $message = $this->session->get('flash_message');
        $this->session->forget('flash_message');
        return $message;
    }
}