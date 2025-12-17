<?php

namespace XMVC\Cache;

interface CacheStoreInterface
{
    public function get($key);
    public function put($key, $value, $seconds);
    public function forget($key);
}
