<?php

namespace XMVC\Cache;

class ArrayStore implements CacheStoreInterface
{
    protected $storage = [];

    public function get($key)
    {
        if (isset($this->storage[$key])) {
            if ($this->storage[$key]['expires_at'] > time()) {
                return $this->storage[$key]['value'];
            }
            unset($this->storage[$key]);
        }
        return null;
    }

    public function put($key, $value, $seconds)
    {
        $this->storage[$key] = [
            'value' => $value,
            'expires_at' => time() + $seconds
        ];
        return true;
    }

    public function forget($key)
    {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
            return true;
        }
        return false;
    }
}
