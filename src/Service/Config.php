<?php

namespace XMVC\Service;

class Config
{
    protected $items = [];

    public function __construct()
    {
        $this->loadAll();
    }

    public function loadAll()
    {
        foreach (glob(BASE_PATH . '/config/*.php') as $file) {
            $this->items[basename($file, '.php')] = require $file;
        }
    }

    public function get($key, $default = null)
    {
        list($file, $item) = explode('.', $key, 2);
        return $this->items[$file][$item] ?? $default;
    }
}