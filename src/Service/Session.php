<?php

namespace XMVC\Service;

class Session
{
    public function __construct()
    {
        $this->start();
    }

    public function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function forget($key)
    {
        unset($_SESSION[$key]);
    }
}